<?php

/**
 * class TM_User_Role
 *
 */
class TM_User_Role
{

    protected $_id;

    protected $_title;

    protected $_rtitle;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;


    public function setId($id)
    {
        $this->_id = (int)$id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setRtitle($rtitle)
    {
        $this->_rtitle = $rtitle;
    }

    public function getRtitle()
    {
        return $this->_rtitle;
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


    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
    }

    public function insertToDB()
    {
        try {
            $sql = 'INSERT INTO tm_user_role(title, rtitle) VALUES (:title, :rtitle)';
            $this->_db->query($sql, array('title' => $this->_title, 'rtitle' => $this->_rtitle));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     *
     *
     * @return void
     */
    public function updateToDB()
    {
        try {
            $sql
                = 'UPDATE tm_user_role SET title=:title, rtitle=:rtitle
                   WHERE id=:id';
            $this->_db->query($sql, array('title' => $this->_title, 'rtitle' => $this->_rtitle, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @return void
     */
    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM tm_user_role WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @param int $id
     *
     * @throws Exception
     * @return TM_User_Role
     * @static
     * @access public
     */
    public static function getInstanceById($id)
    {
        try {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT * FROM tm_user_role WHERE id=:id';
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new TM_User_Role();
                $o->fillFromArray($result[0]);
                return $o;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance()
    {
        try {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT * FROM tm_user_role';
            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = TM_User_Role::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @param array $values
     *
     * @return TM_User_Role
     * @static
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new TM_User_Role();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setRtitle($values['rtitle']);
    }

}
