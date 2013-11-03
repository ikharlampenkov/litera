<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 27.05.12
 * Time: 14:27
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE calendar
 (
   id bigserial NOT NULL,
   link_id bigint NOT NULL,
   date_event date NOT NULL,
   time_event time without time zone,
   title character varying(255) NOT NULL,
   file character varying(255),
   place character varying(255),
   short_text text,
   full_text text,
   date_create date NOT NULL,
   category_id bigint,
   CONSTRAINT calendar_pkey PRIMARY KEY (id),
   CONSTRAINT calendar_category_id_fkey FOREIGN KEY (category_id)
       REFERENCES calendar_category (id) MATCH SIMPLE
       ON UPDATE SET NULL ON DELETE SET NULL
 )
 WITH (
   OIDS=FALSE
 );
 */

class SM_Module_Calendar
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
    protected $_dateEvent;

    /**
     * @var string
     */
    protected $_timeEvent;

    /**
     * @var string
     */
    protected $_place = '';

    /**
     * @var string
     */
    protected $_shortText = '';

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
     * @var SM_Module_CalendarCategory|null
     */
    protected $_category = null;

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
     * @param string $place
     */
    public function setPlace($place)
    {
        $this->_place = $place;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->_place;
    }

    /**
     * @param string $timeEvent
     */
    public function setTimeEvent($timeEvent)
    {
        $this->_timeEvent = $timeEvent;
    }

    /**
     * @return string
     */
    public function getTimeEvent()
    {
        return $this->_timeEvent;
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
    public function setShortText($shortText)
    {
        $this->_shortText = $shortText;
    }

    /**
     * @return string
     */
    public function getShortText()
    {
        return $this->_shortText;
    }

    /**
     * @param string $date_public
     */
    public function setDateEvent($date_public)
    {
        $this->_dateEvent = date('Y-m-d', strtotime($date_public));
    }

    /**
     * @return string
     */
    public function getDateEvent()
    {
        return $this->_dateEvent;
    }

    /**
     * @param null|\SM_Module_CalendarCategory $category
     */
    public function setCategory($category)
    {
        $this->_category = $category;
    }

    /**
     * @return null|\SM_Module_CalendarCategory
     */
    public function getCategory()
    {
        return $this->_category;
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
     * @param null|\SM_Module_CalendarCategory $value
     *
     * @return null
     */
    protected function _prepareNullCategory($value)
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
        $this->_dateEvent = date('Y-m-d');

        $this->_file = new TM_FileManager_Image($config->files->path);
    }

    public function insertToDB()
    {
        try {
            $sql
                = 'INSERT INTO calendar(link_id, title, date_event, time_event, date_create, place, short_text, full_text, category_id)
                             VALUES(:link_id, :title, :date_event, :time_event, :date_create, :place, :short_text, :full_text, :category_id)';
            $this->_db->query(
                $sql, array('link_id'     => $this->_link->getId(), 'title' => $this->_title, 'date_create' => $this->_dateCreate,
                            'date_event'  => $this->_dateEvent, 'time_event' => $this->_timeEvent, 'place' => $this->_place, 'short_text' => $this->_shortText, 'full_text' => $this->_fullText,
                            'category_id' => $this->_prepareNullCategory($this->_category))
            );

            $this->_id = $this->_db->lastInsertId('calendar', 'id');

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_file->createPreview(40, 40);
                $this->_db->query('UPDATE calendar SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //link_id, title, date_event, date_create, short_text, full_text, file

    public function updateToDB()
    {
        try {
            $sql
                = 'UPDATE calendar
                      SET link_id=:link_id, title=:title, date_event=:date_event, time_event=:time_event, date_create=:date_create, place=:place,
                           short_text=:short_text, full_text=:full_text, category_id=:category_id
                    WHERE id=:id';
            $this->_db->query(
                $sql, array('link_id'     => $this->_link->getId(), 'title' => $this->_title, 'date_create' => $this->_dateCreate,
                            'date_event'  => $this->_dateEvent, 'time_event' => $this->_timeEvent, 'place' => $this->_place, 'short_text' => $this->_shortText, 'full_text' => $this->_fullText,
                            'category_id' => $this->_prepareNullCategory($this->_category), 'id' => $this->_id)
            );

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_file->createPreview(40, 40);
                $this->_db->query('UPDATE calendar SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $this->_file->delete();

            $sql = 'DELETE FROM calendar WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param                                 $link
     * @param SM_Module_CalendarCategory|null $category
     *
     * @throws Exception
     * @return array|bool
     */
    public static function getAllInstance($link, $category = null)
    {
        try {
            $db = Zend_Registry::get('db');

            if ($category != null) {
                $sql = 'SELECT * FROM calendar WHERE link_id=:link_id AND category_id=:category_id ORDER BY date_event DESC';
                $bind = array('link_id' => $link->getId(), 'category_id' => $category->getId());
            } else {
                $sql = 'SELECT * FROM calendar WHERE link_id=:link_id ORDER BY date_event DESC';
                $bind = array('link_id' => $link->getId());
            }

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Calendar::getInstanceByArray($res);
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

    public static function getEventByDate($date)
    {
        try {
            $db = Zend_Registry::get('db');

            $sql = 'SELECT * FROM calendar WHERE date_event=:date ORDER BY time_event';
            $bind = array('date' => $date);

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Calendar::getInstanceByArray($res);
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

    public static function getAllTopCalendarInstance()
    {
        try {
            $sql = 'SELECT * FROM calendar ORDER BY date_event DESC LIMIT ' . SM_Module_Calendar::TOP_NEWS_COUNT;
            $bind = array();

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Calendar::getInstanceByArray($res);
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
     * @return SM_Module_Document
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_Calendar();
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
     * @return bool|SM_Module_Calendar
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM calendar WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_Calendar();
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
        $this->setDateEvent($values['date_event']);
        $this->setTimeEvent($values['time_event']);
        $this->setPlace($values['place']);
        $this->setShortText($values['short_text']);
        $this->setFullText($values['full_text']);

        $this->setFile($values['file']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        $this->setLink($oMenuItem);

        $oCategory = SM_Module_CalendarCategory::getInstanceById($values['category_id']);
        if ($oCategory !== false) {
            $this->setCategory($oCategory);
        }
    }
}
