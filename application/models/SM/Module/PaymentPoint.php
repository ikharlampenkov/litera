<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 27.05.12
 * Time: 14:27
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE news
 (
   id bigserial NOT NULL,
   link_id bigint NOT NULL,
   date_public timestamp(3) without time zone NOT NULL,
   title character varying(255) NOT NULL,
   file character varying(255),
   short_text text,
   full_text text,
   date_create date NOT NULL,
   category_id bigint,
   CONSTRAINT news_pkey PRIMARY KEY (id),
   CONSTRAINT news_category_id_fkey FOREIGN KEY (category_id)
       REFERENCES news_category (id) MATCH SIMPLE
       ON UPDATE SET NULL ON DELETE SET NULL
 )
 WITH (
   OIDS=FALSE
 );
 */

class SM_Module_PaymentPoint
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
     * @var string
     */
    protected $_title;

    /**
     * @var string
     */
    protected $_address = '';

    /**
     * @var string
     */
    protected $_fullText = '';

    /**
     * @var TM_FileManager_Image|null
     */
    protected $_file = null;

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
        return stripslashes($this->_title);
    }

    /**
     * @param null|\TM_FileManager_File $file
     */
    protected function setFile($file)
    {
        if (is_null($this->_file)) {
            $this->_file = $file;
        } else {
            $this->_file->setName($file);
        }
    }

    /**
     * @return null|\TM_FileManager_File
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @param string $fullText
     */
    public function setFullText($fullText)
    {
        $this->_fullText = $fullText;
    }

    /**
     * @return string
     */
    public function getFullText()
    {
        return stripslashes($this->_fullText);
    }

    /**
     * @param string $shortText
     */
    public function setAddress($shortText)
    {
        $this->_address = $shortText;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return stripslashes($this->_address);
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
        $config = Zend_Registry::get('production');
        $this->_db = Zend_Registry::get('db');

        $this->_dateCreate = date('Y-m-d');

        $this->_file = new TM_FileManager_Image($config->files->path);
    }

    public function insertToDB()
    {
        try {
            $sql
                = 'INSERT INTO payment_point(link_id, title, date_create, address, full_text)
                             VALUES(:link_id, :title, :date_create, :address, :full_text)';
            $this->_db->query(
                $sql, array('link_id' => $this->_link->getId(), 'title' => $this->_title, 'date_create' => $this->_dateCreate,
                    'address' => $this->_address, 'full_text' => $this->_fullText
                )
            );

            $this->_id = $this->_db->lastInsertId('payment_point', 'id');

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_file->createPreview(150, 100);
                $this->_db->query('UPDATE payment_point SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //link_id, title, date_public, date_create, short_text, full_text, file

    public function updateToDB()
    {
        try {
            $sql
                = 'UPDATE payment_point
                      SET link_id=:link_id, title=:title, date_create=:date_create,
                          address=:address, full_text=:full_text
                    WHERE id=:id';
            $this->_db->query(
                $sql, array('link_id' => $this->_link->getId(), 'title' => $this->_title, 'date_create' => $this->_dateCreate,
                    'address' => $this->_address, 'full_text' => $this->_fullText,
                    'id' => $this->_id)
            );

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_file->createPreview(150, 100);
                $this->_db->query('UPDATE payment_point SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $this->_file->delete();

            $sql = 'DELETE FROM payment_point WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param  SM_Menu_Item $link
     *
     * @throws Exception
     * @return array|bool
     */
    public static function getAllInstance($link)
    {
        try {
            $db = Zend_Registry::get('db');

            $sql = 'SELECT * FROM payment_point WHERE link_id=:link_id';
            $bind = array('link_id' => $link->getId());

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_PaymentPoint::getInstanceByArray($res);
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
     * @return SM_Module_PaymentPoint
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_PaymentPoint();
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
     * @return bool|SM_Module_PaymentPoint
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM payment_point WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_PaymentPoint();
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
     *
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setDateCreate($values['date_create']);
        $this->setAddress($values['address']);
        $this->setFullText($values['full_text']);

        $this->setFile($values['file']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        $this->setLink($oMenuItem);
    }
}
