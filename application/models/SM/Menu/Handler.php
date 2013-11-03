<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 21.05.12
 * Time: 23:15
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `menu_handler` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL,
   `controller` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

class SM_Menu_Handler
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     * @access protected
     */
    protected $_title;

    /**
     * @var string
     * @access protected
     */
    protected $_controller = '';

    protected $_db;


    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
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
        }
    }

    public function __construct()
    {
        $config = Zend_Registry::get('production');
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
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
            $o = new SM_Menu_Handler();
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
            $sql = 'SELECT * FROM menu_handler';

            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Menu_Handler::getInstanceByArray($res);
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
     * @return bool|SM_Menu_Handler
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM menu_handler WHERE id=' . (int)$id;

            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Menu_Handler();
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
        $this->setController($values['controller']);
    }
}
