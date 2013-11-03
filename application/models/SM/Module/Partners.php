<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 16.04.13
 * Time: 22:37
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE partners
 (
   id bigserial NOT NULL,
   link_id bigint,
   title character varying(50) NOT NULL,
   url character varying(255),
   file character varying(255),
   date_create date,
   CONSTRAINT partners_pkey PRIMARY KEY (id),
   CONSTRAINT partners_link_id_fkey FOREIGN KEY (link_id)
       REFERENCES menu_item (id) MATCH SIMPLE
       ON UPDATE CASCADE ON DELETE SET NULL
 )
 WITH (
   OIDS=FALSE
 );
 ALTER TABLE partners
   OWNER TO garage;
 COMMENT ON TABLE partners
   IS 'Таблица партнеров';
 */

class SM_Module_Partners
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var SM_Menu_Item
     */
    protected $_link = null;

    /**
     * @var string
     */
    protected $_title;

    /**
     * @var string
     */
    protected $_url = '';

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
        $this->_title = str_replace('&quot;', "", stripcslashes($title));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
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
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
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
     * @param null|\SM_Menu_Item $value
     *
     * @return null
     */
    protected function _prepareNullLink($value)
    {
        if (is_null($value) || empty($value)) {
            return null;
        } else {
            return $value->getId();
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
            $sql = 'INSERT INTO partners(link_id, title, url, date_create) VALUES(:link_id, :title, :url, :date_create)';
            $this->_db->query(
                $sql, array('link_id' => $this->_prepareNullLink($this->_link), 'title' => $this->_title, 'url' => $this->_url, 'date_create' => $this->_dateCreate)
            );

            $this->_id = $this->_db->lastInsertId('partners', 'id');

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_file->createPreview(40, 40);
                $this->_db->query('UPDATE partners SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
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
                = 'UPDATE partners
                      SET link_id=:link_id, title=:title, url=:url, date_create=:date_create
                    WHERE id=:id';
            $this->_db->query(
                $sql, array('link_id' => $this->_prepareNullLink($this->_link), 'title' => $this->_title, 'url' => $this->_url, 'date_create' => $this->_dateCreate,
                            'id'      => $this->_id)
            );

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_file->createPreview(40, 40);
                $this->_db->query('UPDATE partners SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $this->_file->delete();

            $sql = 'DELETE FROM partners WHERE id=:id';
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
    public static function getAllInstance($link = null)
    {
        try {
            $db = Zend_Registry::get('db');

            if ($link != null) {
                $sql = 'SELECT * FROM partners WHERE link_id=:link_id';
                $bind = array('link_id' => $link->getId());
            } else {
                $sql = 'SELECT * FROM partners WHERE link_id IS NULL';
                $bind = array();
            }

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Partners::getInstanceByArray($res);
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
     * @return SM_Module_Partners
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_Partners();
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
     * @return bool|SM_Module_Partners
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM partners WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_Partners();
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
        $this->setUrl($values['url']);
        $this->setDateCreate($values['date_create']);

        $this->setFile($values['file']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        if ($oMenuItem != false) {
            $this->setLink($oMenuItem);
        }
    }

}