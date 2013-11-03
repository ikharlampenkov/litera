<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 05.06.12
 * Time: 12:30
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `phone` (
   `id` int(10) unsigned NOT NULL auto_increment,
   `address` varchar(255) NOT NULL,
   `phone` varchar(255) NOT NULL,
   `post_index` varchar(255) NOT NULL,
   PRIMARY KEY  (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

class SM_Module_Phone
{
    /**
     * @var int
     */
    protected $_id;


    /**
     * @var string
     */
    protected $_address = '';

    /**
     * @var string
     */
    protected $_phone = '';

    /**
     * @var string
     */
    protected $_post_index = '';

    /**
     * @var string
     */
    protected $_dateCreate;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

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
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->_phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * @param string $post_index
     */
    public function setPostIndex($post_index)
    {
        $this->_post_index = $post_index;
    }

    /**
     * @return string
     */
    public function getPostIndex()
    {
        return $this->_post_index;
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

    public function  __construct()
    {
        $this->_db = Zend_Registry::get('db');

        $this->_dateCreate = date('Y-m-d');
    }

    public function insertToDB()
    {
        try {
            $sql = 'INSERT INTO phone(address, phone, post_index, date_create)
                         VALUES(:address, :phone, :post_index, :date_create)';
            $this->_db->query($sql, array('address' => $this->_address, 'phone' => $this->_phone, 'date_create' => $this->_dateCreate, 'post_index' => $this->_post_index));

            $this->_id = $this->_db->lastInsertId('phone', 'id');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //address, phone, post_index

    public function updateToDB()
    {
        try {
            $sql = 'UPDATE phone
                       SET address=:address, phone=:phone, post_index=:post_index, date_create=:date_create
                     WHERE id=:id';
            $this->_db->query($sql, array('address' => $this->_address, 'phone' => $this->_phone, 'date_create' => $this->_dateCreate, 'post_index' => $this->_post_index, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM phone WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $values
     * @return SM_Module_Document
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_Phone();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $id
     * @return bool|SM_Module_Phone
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM phone WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_Phone();
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
        $this->setAddress($values['address']);
        $this->setPhone($values['phone']);
        $this->setPostIndex($values['post_index']);
        $this->setDateCreate($values['date_create']);
    }

}
