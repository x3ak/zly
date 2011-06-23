<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: User.php 1231 2011-04-17 17:49:48Z deeper $
 * @license New BSD
 */
namespace User\Model\Mapper;

/**
 * @Entity(repositoryClass="User\Model\DbTable\User")
 * @Table(name="user_users")
 */
class User
{
    /**
    * @Id 
    * @Column(type="integer")
    * @GeneratedValue
    */
    protected $id;

    /** @Column(length=50) */
    protected $login;
    
    /** @Column(length=32) */
    protected $password;
    
    /** @Column(type="integer", nullable=true) */
    protected $role_id;
    
    /** @Column(type="boolean") */
    protected $active;
    
    /** @Column(length=255, nullable=true) */
    protected $firstname;
    
    /** @Column(length=255, nullable=true) */
    protected $lastname;
    
    /** @Column(length=255, nullable=true) */
    protected $patronymic;
    
    /** @Column(length=255, nullable=true) */
    protected $email;
    
    /** @Column(length=255, nullable=true) */
    protected $phone;
    
    /** @Column(length=255, nullable=true) */
    protected $region;
    
    /** @Column(length=255, nullable=true) */
    protected $city;
    
    /** @Column(length=255, nullable=true) */
    protected $zip;
    
    /** @Column(length=255, nullable=true) */
    protected $address;
    
    /** @Column(length=255, nullable=true) */
    protected $mobile_code;
    
    /** @Column(length=255, nullable=true) */
    protected $mobile_number;

    /**
     * @OneToOne(targetEntity="User\Model\Mapper\Role")
     * @JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $role;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRoleId() {
        return $this->role_id;
    }

    public function setRoleId($role_id) {
        $this->role_id = $role_id;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function getPatronymic() {
        return $this->patronymic;
    }

    public function setPatronymic($patronymic) {
        $this->patronymic = $patronymic;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getZip() {
        return $this->zip;
    }

    public function setZip($zip) {
        $this->zip = $zip;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getMobileCode() {
        return $this->mobile_code;
    }

    public function setMobileCode($mobile_code) {
        $this->mobile_code = $mobile_code;
    }

    public function getMobileNumber() {
        return $this->mobile_number;
    }

    public function setMobileNumber($mobile_number) {
        $this->mobile_number = $mobile_number;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }
    
    public function toArray()
    {
        $array = array();
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');
        
        $vars = get_class_vars(get_class($this));
        foreach(array_keys($vars) as $var) {
            $array[$var] = $this->{'get'.$filter->filter($var)}();
        }
        return $array;
    }
    
    public function fromArray($data)
    {
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');
        
        $vars = get_class_vars(get_class($this));
        foreach(array_keys($vars) as $var) {
            if(isset($data[$var]))
            $this->{'set'.$filter->filter($var)}($data[$var]);
        }
        return $this;
    }
}

