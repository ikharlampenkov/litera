<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 12.04.13
 * Time: 21:45
 * To change this template use File | Settings | File Templates.
 */

/*
 *
 CREATE TABLE IF NOT EXISTS `city` (
   `id` bigint(20) NOT NULL auto_increment,
   `title` varchar(255) default NULL,
   `code` varchar(50) NOT NULL,
   `date_create` date NOT NULL,
   PRIMARY KEY  (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

/**
 * Class SM_Module_City
 */

class SM_Module_City
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @var string
     */
    protected $_dateCreate = '';

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->_code = mb_convert_case(StdLib_Functions::translitIt($code), MB_CASE_LOWER, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param string $dateCreate
     */
    public function setDateCreate($dateCreate)
    {
        $this->_dateCreate = date('Y-m-d', strtotime($dateCreate));
    }

    /**
     * @return string
     */
    public function getDateCreate()
    {
        return $this->_dateCreate;
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

        $this->_dateCreate = date('Y-m-d');
    }

    public function insertToDB()
    {
        try {
            $sql = 'INSERT INTO city(title, code, date_create) VALUES(:title, :code, :date_create)';
            $this->_db->query($sql, array('title' => $this->_title, 'code' => $this->_code, 'date_create' => $this->_dateCreate));

            $this->_id = $this->_db->lastInsertId('city', 'id');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //id, title, date_create

    public function updateToDB()
    {
        try {
            $sql = 'UPDATE city
                       SET title=:title, code=:code, date_create=:date_create
                     WHERE id=:id';
            $this->_db->query($sql, array('title' => $this->_title, 'code' => $this->_code, 'date_create' => $this->_dateCreate, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM city WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @return array|bool
     * @throws Exception
     */
    public static function getAllInstance()
    {
        try {
            $sql = 'SELECT * FROM city';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_City::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $values
     * @return SM_Module_City
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_City();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $id
     * @return bool|SM_Module_City
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM city WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetch();

            if (!empty($result)) {
                $o = new SM_Module_City();
                $o->fillFromArray($result);
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
     * @param array $values
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setCode($values['code']);
        $this->setDateCreate($values['date_create']);
    }


}