<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 26.11.11
 * Time: 14:47
 * To change this template use File | Settings | File Templates.
 */

class TM_User_Resource
{

    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
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


    /**
     * @return TM_User_Resource
     */
    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
    }

    /**
     * @throws Exception
     * @return void
     */

    public function insertToDB()
    {
        try {
            $sql = 'INSERT INTO tm_user_resource(title, rtitle) VALUES (:title, :rtitle)';
            $this->_db->query($sql, array('title' => $this->_title, 'rtitle' => $this->_rtitle));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     *
     *
     * @return void
     * @access public
     */
    public function updateToDB()
    {
        try {
            $sql = 'UPDATE tm_user_resource SET title=:title, rtitle=:rtitle WHERE id=:id';
            $this->_db->query($sql, array('title' => $this->_title, 'rtitle' => $this->_rtitle, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @return void
     * @access public
     */
    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM tm_user_resource WHERE id=:id';
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
     * @return TM_User_Resource
     * @static
     * @access public
     */
    public static function getInstanceById($id)
    {
        try {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT * FROM tm_user_resource WHERE id=:id';
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new TM_User_Resource();
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
            $sql = 'SELECT * FROM tm_user_resource ORDER BY title';
            $result = $db->query($sql, array())->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = TM_User_Resource::getInstanceByArray($res);
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
     * @throws Exception
     * @return TM_User_Resource
     * @static
     * @access public
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new TM_User_Resource();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function checkResource($resource)
    {
        try {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT COUNT(id) AS cnt FROM tm_user_resource WHERE title=:title';
            $result = $db->query($sql, array('title' => $resource))->fetch();

            if (isset($result['cnt']) && $result['cnt'] == 1) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setRtitle($values['rtitle']);
    }

}
