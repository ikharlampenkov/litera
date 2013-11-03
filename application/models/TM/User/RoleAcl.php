<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 27.11.11
 * Time: 17:59
 * To change this template use File | Settings | File Templates.
 */

/*
 * tm_user_role_id,
  tm_user_resource_id,
  is_allow,
  privileges
  */

class TM_User_RoleAcl
{

    /**
     * @var TM_User_Role
     */
    protected $_role;

    /**
     * @var TM_User_Resource
     */
    protected $_resource;

    /**
     * @var bool
     */
    protected $_isAllow = 0;

    /**
     * @var string
     */
    protected $_privileges = '';

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;


    /**
     * @param boolean $isAllow
     */
    public function setIsAllow($isAllow)
    {
        if ($isAllow == 'on') {
            $this->_isAllow = 1;
        } elseif (empty($isAllow)) {
            $this->_isAllow = 0;
        } else {
            $this->_isAllow = $isAllow;
        }
    }

    /**
     * @return boolean
     */
    public function getIsAllow()
    {
        return $this->_isAllow;
    }

    /**
     * @param string $privileges
     */
    public function setPrivileges($privileges)
    {
        $this->_privileges = $privileges;
    }

    /**
     * @param bool $asArray
     *
     * @return string|array
     */
    public function getPrivileges($asArray = false)
    {
        if ($asArray) {
            return explode(',', $this->_privileges);
        } else {
            return $this->_privileges;
        }
    }

    /**
     * @param TM_User_Resource $resource
     */
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }

    /**
     * @return TM_User_Resource
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * @param TM_User_Role $role
     */
    public function setRole($role)
    {
        $this->_role = $role;
    }

    /**
     * @return TM_User_Role
     */
    public function getRole()
    {
        return $this->_role;
    }


    public function __get($name)
    {
        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            throw new Exception('Can not find method ' . $method . ' in class ' . __CLASS__);
        }
    }

    /**
     * @param TM_User_Role $role
     *
     * @return TM_User_RoleAcl
     */
    public function __construct(TM_User_Role $role)
    {
        $this->_db = Zend_Registry::get('db');
        $this->_role = $role;
    }

    /**
     *
     *
     * @return void
     * @access public
     */
    public function saveToDb()
    {
        try {
            $this->_db->beginTransaction();
            $sql = 'DELETE FROM tm_acl_role WHERE tm_user_role_id=:tm_user_role_id AND tm_user_resource_id=:tm_user_resource_id';
            $this->_db->query($sql, array('tm_user_role_id' => $this->_role->getId(), 'tm_user_resource_id' => $this->_resource->getId()));

            $sql
                = 'INSERT INTO tm_acl_role(tm_user_role_id, tm_user_resource_id, is_allow, privileges)
                    VALUES (:tm_user_role_id, :tm_user_resource_id, :is_allow, :privileges)';
            $this->_db->query(
                $sql, array('tm_user_role_id' => $this->_role->getId(), 'tm_user_resource_id' => $this->_resource->getId(),
                            'is_allow'        => $this->_isAllow, 'privileges' => $this->_privileges)
            );
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    //tm_user_role_id, tm_user_resource_id, is_allow, privileges

    /**
     *
     * @param TM_User_Role $role
     * @param array        $values
     *
     * @throws Exception
     * @return TM_User_RoleAcl
     * @static
     * @access public
     */
    public static function getInstanceByArray(TM_User_Role $role, $values)
    {
        try {
            $o = new TM_User_RoleAcl($role);
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function getInstanceByArray

    /**
     *
     * @param TM_User_Role $role
     *
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance(TM_User_Role $role)
    {
        try {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT * FROM tm_acl_role WHERE tm_user_role_id=:tm_user_role_id';
            $result = $db->query($sql, array('tm_user_role_id' => $role->getId()))->fetchAll();;

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[$res['tm_user_resource_id']] = TM_User_RoleAcl::getInstanceByArray($role, $res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function getAllInstance

    /**
     *
     *
     * @param array $values
     *
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setResource(TM_User_Resource::getInstanceById($values['tm_user_resource_id']));
        $this->setIsAllow($values['is_allow']);
        $this->setPrivileges($values['privileges']);
    } // end of member function fillFromArray

}