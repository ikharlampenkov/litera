<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 21.05.12
 * Time: 23:15
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `menu_menu` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL,
   `code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

class SM_Menu_Menu
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string название меню
     * @access protected
     */
    protected $_title;

    /**
     * @var string код на английском
     * @access protected
     */
    protected $_code = '';

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;


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

    public function insertToDb()
    {
        try {
            $sql = 'INSERT INTO menu_menu(title, code) VALUES (:title, :code)';
            $this->_db->query($sql, array('title' => $this->_title, 'code' => $this->_code));

            $this->_id = $this->_db->lastInsertId('menu_menu', 'id');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateToDb()
    {
        try {
            $sql = 'UPDATE menu_menu SET title=:title, code=:code
                    WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id, 'title' => $this->_title, 'code' => $this->_code));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function deleteFromDb()
    {
        try {
            $sql = 'DELETE FROM menu_menu WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * @static
     * @param $values
     * @return SM_Menu_Handler
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Menu_Menu();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @throws Exception
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance()
    {
        try {
            $sql = 'SELECT * FROM menu_menu';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Menu_Menu::getInstanceByArray($res);
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
     * @static
     * @param $id
     * @return bool|SM_Menu_Menu
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM menu_menu WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Menu_Menu();
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
     * @static
     * @param $code код блока меню
     * @throws Exception
     * @internal param $link
     * @return bool|SM_Menu_Menu
     */
    public static function getInstanceByCode($code)
    {
        try {
            $sql = 'SELECT * FROM menu_menu WHERE code=:code';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('code' => $code))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Menu_Menu();
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
     * @param array $values
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setCode($values['code']);
    }

    public function getItemList()
    {
        try {
            $result = SM_Menu_Item::getAllInstanceByMenu($this);
            return $result;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
