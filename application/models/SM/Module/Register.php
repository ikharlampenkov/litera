<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 30.05.13
 * Time: 23:24
 * To change this template use File | Settings | File Templates.
 */

/*
 * ООО Управляющая компания «Жилищный трест Кировского района»
 ОГРН: 1114205039035
 Юридический адрес: 650001, Кемеровская обл., г.Кемерово, ул.Потемкина, 5;
 Почтовый адрес: 650001, Кемеровская обл., г.Кемерово, ул.40 лет Октября, д.9 "б";
 тел./факс: 8 (3842) 61-94-00;
 Директор: Гениятов Вячеслав Листальевич
 */

class SM_Module_Register
{

    /**
     * @var int
     */
    protected $_id;

    /**
     * @var SM_Menu_Item
     */
    protected $_link;

    /**
     * @var SM_Module_City|null
     */
    protected $_city = null;

    /**
     * @var string
     */
    protected $_title = '';

    protected $_ogrn = '';

    protected $_registeredAddress = '';

    protected $_postalAddress = '';

    protected $_phone = '';

    protected $_site = '';

    protected $_director = '';

    /**
     * @var string
     */
    protected $_dateCreate = '';

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @param \SM_Menu_Item $link
     */
    public function setLink($link)
    {
        $this->_link = $link;
    }

    /**
     * @return \SM_Menu_Item
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * @param null|\SM_Module_City $city
     */
    public function setCity($city)
    {
        $this->_city = $city;
    }

    /**
     * @return null|\SM_Module_City
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * @param string $director
     */
    public function setDirector($director)
    {
        $this->_director = $director;
    }

    /**
     * @return string
     */
    public function getDirector()
    {
        return $this->_director;
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
     * @param string $ogrn
     */
    public function setOgrn($ogrn)
    {
        $this->_ogrn = $ogrn;
    }

    /**
     * @return string
     */
    public function getOgrn()
    {
        return $this->_ogrn;
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
     * @param string $site
     */
    public function setSite($site)
    {
        $this->_site = str_replace('/', '', str_replace('http://', '', $site));
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * @param string $postalAddress
     */
    public function setPostalAddress($postalAddress)
    {
        $this->_postalAddress = $postalAddress;
    }

    /**
     * @return string
     */
    public function getPostalAddress()
    {
        return $this->_postalAddress;
    }

    /**
     * @param string $registeredAddress
     */
    public function setRegisteredAddress($registeredAddress)
    {
        $this->_registeredAddress = $registeredAddress;
    }

    /**
     * @return string
     */
    public function getRegisteredAddress()
    {
        return $this->_registeredAddress;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = stripslashes($title);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
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
            $sql
                    = 'INSERT INTO register(link_id, city_id, title, ogrn, registered_address, postal_address, phone, director, site, date_create)
                                    VALUES(:link_id, :city_id, :title, :ogrn, :registered_address, :postal_address, :phone, :director, :site, :date_create)';
            $this->_db->query(
                $sql, array('link_id' => $this->_link->getId(), 'city_id' => $this->_city->getId(), 'title' => $this->_title, 'ogrn' => $this->_ogrn,
                    'registered_address' => $this->_registeredAddress, 'postal_address' => $this->_postalAddress, 'phone' => $this->_phone,
                    'director' => $this->_director, 'site' => $this->_site, 'date_create' => $this->_dateCreate)
            );

            $this->_id = $this->_db->lastInsertId('register', 'id');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //id, link_id, city_id, title, ogrn, registered_address, postal_address, phone, director

    public function updateToDB()
    {
        try {
            $sql
                    = 'UPDATE register
                           SET link_id=:link_id, city_id=:city_id, title=:title, ogrn=:ogrn, registered_address=:registered_address, postal_address=:postal_address,
                               phone=:phone, director=:director, site=:site, date_create=:date_create
                         WHERE id=:id';
            $this->_db->query(
                $sql, array('link_id' => $this->_link->getId(), 'city_id' => $this->_city->getId(), 'title' => $this->_title, 'ogrn' => $this->_ogrn,
                'registered_address' => $this->_registeredAddress, 'postal_address' => $this->_postalAddress, 'phone' => $this->_phone,
                'director' => $this->_director, 'site' => $this->_site, 'date_create' => $this->_dateCreate, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM register WHERE id=:id';
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
    public static function getAllInstance($link, $city = null)
    {
        try {
            $db = Zend_Registry::get('db');

            if ($city != null) {
                $sql = 'SELECT * FROM register WHERE link_id=:link_id AND city_id=:city_id ORDER BY title DESC';
                $bind = array('link_id' => $link->getId(), 'city_id' => $city->getId());
            } else {
                $sql = 'SELECT * FROM register WHERE link_id=:link_id ORDER BY title DESC';
                $bind = array('link_id' => $link->getId());
            }

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Register::getInstanceByArray($res);
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
     *
     * @param $values
     *
     * @return SM_Module_Register
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_Register();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param $id
     *
     * @return bool|SM_Module_Register
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM register WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetch();

            if (!empty($result)) {
                $o = new SM_Module_Register();
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
     *
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setOgrn($values['ogrn']);
        $this->setRegisteredAddress($values['registered_address']);
        $this->setPostalAddress($values['postal_address']);
        $this->setPhone($values['phone']);
        $this->setSite($values['site']);
        $this->setDirector($values['director']);

        $this->setDateCreate($values['date_create']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        if ($oMenuItem !== false) {
            $this->setLink($oMenuItem);
        }

        $oCity = SM_Module_City::getInstanceById($values['city_id']);
        if ($oCity !== false) {
            $this->setCity($oCity);
        }
    }


}