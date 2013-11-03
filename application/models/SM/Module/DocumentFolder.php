<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 25.05.12
 * Time: 21:26
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `document_folder` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `link_id` int(10) unsigned NOT NULL,
   `parent_id` int(10) unsigned DEFAULT NULL,
   `title` varchar(255) NOT NULL,
   `date_create` date NOT NULL,
   PRIMARY KEY (`id`),
   KEY `link_id` (`link_id`),
   KEY `parent_id` (`parent_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

class SM_Module_DocumentFolder
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
     * @var SM_Module_DocumentFolder|null
     */
    protected $_parent = null;

    /**
     * @var string
     */
    protected $_title;

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
     * @param null|\SM_Module_DocumentFolder $parent
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }

    /**
     * @return null|\SM_Module_DocumentFolder
     */
    public function getParent()
    {
        return $this->_parent;
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

    protected function _prepareNull($value)
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
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

        $this->_dateCreate = date('Y-m-d');
        $this->_parent = null;
    }

    public function insertToDB()
    {
        try {
            $sql = 'INSERT INTO document_folder(link_id, parent_id, title, date_create)
                                VALUES(:link_id, :parent_id, :title, :date_create)';
            $this->_db->query($sql, array('link_id' => $this->_link->getId(), 'parent_id' => $this->_prepareNull($this->_parent), 'title' => $this->_title, 'date_create' => $this->_dateCreate));

            $this->_id = $this->getLastInsertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //link_id, parent_id, title, date_create

    public function updateToDB()
    {
        try {
            $sql = 'UPDATE document_folder
                       SET link_id=:link_id, parent_id=:parent_id, title=:title, date_create=:date_create
                     WHERE id=:id';
            $this->_db->query($sql, array('link_id' => $this->_link->getId(), 'parent_id' => $this->_prepareNull($this->_parent),
                'title' => $this->_title, 'date_create' => $this->_dateCreate, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM document_folder WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $link
     * @param $folder
     * @return array|bool
     * @throws Exception
     */
    public static function getAllInstance($link, $folder = 0)
    {
        try {
            $sql = 'SELECT * FROM document_folder WHERE link_id=:link_id ';
            $bind = array('link_id' => $link->getId());

            if ($folder != 0) {
                $sql .= ' AND parent_id=:parent_id';
                $bind['parent_id'] = $folder;
            } else {
                $sql .= ' AND parent_id IS NULL';
            }

            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);


            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_DocumentFolder::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_DocumentFolder();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $id
     * @return bool|SM_Module_DocumentFolder
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM document_folder WHERE id=:id';
            $config = Zend_Registry::get('production');

            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_DocumentFolder();
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
        $this->setDateCreate($values['date_create']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        $this->setLink($oMenuItem);

        $oParent = SM_Module_DocumentFolder::getInstanceById($values['parent_id']);
        if ($oParent !== false) {
            $this->setParent($oParent);
        }
    }

    public function getPathToDocument(&$pathArray = array())
    {
        try {
            if (!is_null($this->_parent)) {
                $pathArray[] = $this->_parent;
                $this->_parent->getPathToDocument($pathArray);
            }
            return array_reverse($pathArray);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function getLastInsertId()
    {
        try {
            $sql = 'SELECT LAST_INSERT_ID()';
            $id = $this->_db->query($sql)->fetchAll(Zend_Db::FETCH_NUM);
            if (isset($id[0][0])) {
                return $id[0][0];
            } else {
                return 0;
            }
        } catch (StdLib_Exception $s_e) {
            throw new StdLib_Exception('Can`t return last id');
        }
    }
}
