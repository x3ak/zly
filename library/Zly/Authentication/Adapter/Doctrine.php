<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Slys\Authentication\Adapter;

use Zend\Authentication\Adapter as AuthenticationAdapter,
    Zend\Authentication\Result as AuthenticationResult,
    Zend\Db\Db,
    Zend\Db\Adapter\AbstractAdapter as AbstractDBAdapter,
    Zend\Db\Expr as DBExpr,
    Zend\Db\Select as DBSelect,
    Zend\Db\Table\AbstractTable;

/**
 * @uses       Zend\Authentication\Adapter\Exception
 * @uses       Zend\Authentication\Adapter
 * @uses       Zend\Authentication\Result
 * @uses       Zend_Db_Adapter_Abstract
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Doctrine implements AuthenticationAdapter
{

  /**
     * Doctrine EntityManager
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The entity name to check for an identity.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Field to be used as identity.
     *
     * @var string
     */
    protected $identityField;

    /**
     * The field to be used as credential.
     *
     * @var string
     */
    protected $credentialField;

    /**
     * Constructor sets configuration options.
     *
     * @param  Doctrine\ORM\EntiyManager
     * @param  string
     * @param  string
     * @param  string
     * @return void
     */
    public function __construct($em, $entityName = null, $identityField = null, $credentialField = null)
    {
        $this->em = $em;

        if (null !== $entityName) {
            $this->setEntityName($entityName);
        }

        if (null !== $identityField) {
            $this->setIdentityField($identityField);
        }

        if (null !== $credentialField) {
            $this->setCredentialField($credentialField);
        }
    }

    /**
     * Set entity name.
     *
     * @param  string
     * @return LoSo_Zend_Auth_Adapter_Doctrine2
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
        return $this;
    }

    /**
     * Set identity field.
     *
     * @param  string
     * @return LoSo_Zend_Auth_Adapter_Doctrine2
     */
    public function setIdentityField($identityField)
    {
        $this->identityField = $identityField;
        return $this;
    }

    /**
     * Set credential field.
     *
     * @param  string
     * @return LoSo_Zend_Auth_Adapter_Doctrine2
     */
    public function setCredentialField($credentialField)
    {
        $this->credentialField = $credentialField;
        return $this;
    }

    /**
     * Set the value to be used as identity.
     *
     * @param  string
     * @return LoSo_Zend_Auth_Adapter_Doctrine2
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Set the value to be used as credential.
     *
     * @param  string
     * @return LoSo_Zend_Auth_Adapter_Doctrine2
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    /**
     * Defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        $query = $this->_getQuery();

        $authResult = array(
            'code'     => \Zend\Authentication\Result::FAILURE,
            'identity' => null,
            'messages' => array()
        );

        try {
            $result = $query->execute(array(1 => $this->identity));

            $resultCount = count($result);
            if ($resultCount > 1) {
                
                $authResult['code'] = \Zend\Authentication\Result::FAILURE_IDENTITY_AMBIGUOUS;
                $authResult['messages'][] = 'More than one entity matches the supplied identity.';
            } else if ($resultCount < 1) {
                $authResult['code'] = \Zend\Authentication\Result::FAILURE_IDENTITY_NOT_FOUND;
                $authResult['messages'][] = 'A record with the supplied identity could not be found.';
            } else if (1 == $resultCount) {
                $this->_identity = $result[0][0];
                if ($result[0][$this->credentialField] != $this->credential) {
                    $authResult['code'] = \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
                    $authResult['messages'][] = 'Supplied credential is invalid.';
                } else {
                    $authResult['code'] = \Zend\Authentication\Result::SUCCESS;
                    $authResult['identity'] = $this->identity;
                    $authResult['messages'][] = 'Authentication successful.';
                }
            }
        } catch (\Doctrine\ORM\Query\QueryException $qe) {
            $authResult['code'] = \Zend\Authentication\Result::FAILURE_UNCATEGORIZED;
            $authResult['messages'][] = $qe->getMessage();
        }

        return new \Zend\Authentication\Result(
            $authResult['code'],
            $authResult['identity'],
            $authResult['messages']
        );
    }

    /**
     * This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws Zend_Auth_Adapter_Exception - in the event that setup was not done properly
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if (null === $this->em || !$this->em instanceof \Doctrine\ORM\EntityManager) {
            $exception = 'A Doctrine2 EntityManager must be supplied for the Zend_Auth_Adapter_Doctrine2 authentication adapter.';
        } elseif (empty($this->identityField)) {
            $exception = 'An identity field must be supplied for the Zend_Auth_Adapter_Doctrine2 authentication adapter.';
        } elseif (empty($this->credentialField)) {
            $exception = 'A credential field must be supplied for the Zend_Auth_Adapter_Doctrine2 authentication adapter.';
        } elseif (empty($this->identity)) {
            $exception = 'A value for the identity was not provided prior to authentication with Zend_Auth_Adapter_Doctrine2.';
        } elseif (empty($this->credential)) {
            $exception = 'A credential value was not provided prior to authentication with Zend_Auth_Adapter_Doctrine2.';
        }

        if (null !== $exception) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            throw new Zend_Auth_Adapter_Exception($exception);
        }
    }

    /**
     * Construct the Doctrine query.
     *
     * @return Doctrine\ORM\Query
     */
    protected function _getQuery()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e.' . $this->credentialField . ', e')
            ->from($this->entityName, 'e')
            ->where('e.' . $this->identityField . ' = ?1');

        return $qb->getQuery();
    }
    
    public function getResultRowObject()
    {
        return $this->_identity;
    }
}
