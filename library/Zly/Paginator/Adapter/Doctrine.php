<?php
namespace Zly\Paginator\Adapter;

use Zend\Paginator\Adapter;

class Doctrine implements Adapter
{
    /**
     * The SELECT query to paginate
     *
     * @var Query
     */
    protected $query = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $rowCount = null;

    /**
     * Use Array Result
     *
     * @var boolean
     */
    protected $arrayResult = false;

    /**
     * Namespace to use for bound parameters
     * If you use :pgid_# as a parameter, then
     * you must change this.
     *
     * @var string
     */
    protected $namespace = 'pgid';

    /**
     * Constructor
     *
     * @param Query $query
     * @param string $ns Namespace to prevent named parameter conflicts
     */
    public function __construct(\Doctrine\ORM\AbstractQuery $query, $ns = 'pgid')
    {
        $this->query = $query;
        $this->namespace = $ns;
    }

    /**
     * Set use array result flag
     *
     * @param boolean $flag True to use array result
     */
    public function useArrayResult($flag = true)
    {
        $this->arrayResult = $flag;
    }

    /**
     * Sets the total row count for this paginator
     *
     * Can be either an integer, or a Doctrine\ORM\Query object
     * which returns the count
     *
     * @param Query|integer $rowCount
     * @return void
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof \Doctrine\ORM\AbstractQuery) {
            $this->rowCount = $rowCount->getSingleScalarResult();
        } else if (is_integer($rowCount)) {
            $this->rowCount = $rowCount;
        } else {
            throw new \InvalidArgumentException("Invalid row count");
        }
    }

    /**
     * Sets the namespace to be used for named parameters
     *
     * Parameters will be in the format 'namespace_1' ... 'namespace_N'
     *
     * @param string $ns
     * @return void
     * @author David Abdemoulaie
     */
    public function setNamespace($ns)
    {
        $this->namespace = $ns;
    }

    /**
     * Gets the current page of items
     *
     * @param string $offset
     * @param string $itemCountPerPage
     * @return void
     * @author David Abdemoulaie
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $ids = $this->createLimitSubquery($offset, $itemCountPerPage)
            ->getScalarResult();

        $ids = array_map(
            function ($e) { return current($e); },
            $ids
        );

        if ($this->arrayResult) {
            return $this->createWhereInQuery($ids)->getArrayResult();
        } else {
            return $this->createWhereInQuery($ids)->getResult();
        }
    }

    /**
     * @param Query $query
     * @return int
     */
    public function count()
    {
        if (is_null($this->rowCount)) {
            $this->setRowCount(
                $this->createCountQuery()
            );
        }
        return $this->rowCount;
    }

    /**
     * @return Query
     */
    protected function createCountQuery()
    {
        return \DoctrineExtensions\Paginate\Paginate::createCountQuery($this->query);
    }

    /**
     * @return Query
     */
    protected function createLimitSubquery($offset, $itemCountPerPage)
    {
        return \DoctrineExtensions\Paginate\Paginate::createLimitSubQuery($this->query, $offset, $itemCountPerPage);
    }

    /**
     * @return Query
     */
    protected function createWhereInQuery($ids)
    {
        return \DoctrineExtensions\Paginate\Paginate::createWhereInQuery($this->query, $ids, $this->namespace);
    }
}