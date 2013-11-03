<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 25.05.12
 * Time: 21:26
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `document` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `link_id` int(10) unsigned NOT NULL,
   `parent_id` int(10) unsigned NOT NULL,
   `title` varchar(255) NOT NULL,
   `short_text` text,
   `full_text` longtext NOT NULL,
   `file` varchar(255) DEFAULT NULL,
   `date_create` date NOT NULL,
   PRIMARY KEY (`id`),
   KEY `fk_document_document1` (`parrent_id`),
   KEY `link_id` (`link_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

class SM_Module_Document
{

    const TOP_DOCUMENT_COUNT = 5;

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
    protected $_shortText = '';

    /**
     * @var string
     */
    protected $_fullText = '';

    /**
     * @var TM_FileManager_File|null
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
        return $this->_fullText;
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

        $this->_file = new TM_FileManager_File($config->files->path);
    }

    public function insertToDB()
    {
        try {
            $sql = 'INSERT INTO document(link_id, parent_id, title, date_create, short_text, full_text)
                                VALUES(:link_id, :parent_id, :title, :date_create, :short_text, :full_text)';
            $this->_db->query($sql, array('link_id' => $this->_link->getId(), 'parent_id' => $this->_prepareNull($this->_parent),
                'title' => $this->_title, 'date_create' => $this->_dateCreate, 'short_text' => $this->_shortText, 'full_text' => $this->_fullText));

            $this->_id = $this->getLastInsertId();

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_db->query('UPDATE document SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }

            $this->_db->query('INSERT INTO document_search(id, search_text) VALUES(:id, :search_text)', array('search_text' => $this->_fullText, 'id' => $this->_id));

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //link_id, parent_id, title, date_create, short_text, full_text, file

    public function updateToDB()
    {
        try {
            $sql = 'UPDATE document
                       SET link_id=:link_id, parent_id=:parent_id, title=:title, date_create=:date_create,
                           short_text=:short_text, full_text=:full_text
                     WHERE id=:id';
            $this->_db->query($sql, array('link_id' => $this->_link->getId(), 'parent_id' => $this->_prepareNull($this->_parent),
                'title' => $this->_title, 'date_create' => $this->_dateCreate, 'short_text' => $this->_shortText, 'full_text' => $this->_fullText, 'id' => $this->_id));

            $fileName = $this->_file->download('file');
            if ($fileName !== false) {
                $this->_db->query('UPDATE document SET file=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }

            $this->_db->query('UPDATE document_search SET search_text=:search_text WHERE id=:id', array('search_text' => $this->_fullText, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $this->_file->delete();

            $this->_db->query('DELETE FROM document_search WHERE id=:id', array('id' => $this->_id));

            $sql = 'DELETE FROM document WHERE id=:id';
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
            $sql = 'SELECT * FROM document WHERE link_id=:link_id ';
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
                    $retArray[] = SM_Module_Document::getInstanceByArray($res);
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

    public static function getTopDocument($link)
    {
        try {
            $sql = 'SELECT * FROM document WHERE link_id=:link_id ORDER BY date_create LIMIT ' . SM_Module_Document::TOP_DOCUMENT_COUNT;
            $bind = array('link_id' => $link->getId());

            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Document::getInstanceByArray($res);
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
     * @return SM_Module_Document
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_Document();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     * @param $id
     * @return bool|SM_Module_Document
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM document WHERE id=:id';
            $config = Zend_Registry::get('production');

            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_Document();
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
        $this->setShortText($values['short_text']);
        $this->setFullText($values['full_text']);

        $this->setFile($values['file']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        $this->setLink($oMenuItem);

        $oParent = SM_Module_DocumentFolder::getInstanceById($values['parent_id']);
        if ($oParent !== false) {
            $this->setParent($oParent);
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

    public static function search($link, $query)
    {
        try {

            $words = explode(' ', $query);
            foreach ($words as $word) {
                if (mb_strlen($word, 'UTF-8') <= 3) {
                    $new_words[] = $word;
                } elseif (mb_strlen($word, 'UTF-8') == 4) {
                    $new_words[] = $word . '*';
                } elseif (mb_strlen($word, 'UTF-8') == 5) {
                    $new_words[] = mb_substr($word, 0, -1, 'UTF-8') . '*';
                } elseif (mb_strlen($word, 'UTF-8') == 6) {
                    $new_words[] = mb_substr($word, 0, -1, 'UTF-8') . '*';
                } elseif (mb_strlen($word, 'UTF-8') == 7) {
                    $new_words[] = mb_substr($word, 0, -1, 'UTF-8') . '*';
                } elseif (mb_strlen($word, 'UTF-8') >= 8) {
                    $new_words[] = mb_substr($word, 0, -2, 'UTF-8') . '*';
                }
            }
            $search_words = implode(' +', $new_words);
            //echo $search_words;

            if (count($new_words) >= 2) {
                $search_wordsH = '+' . $new_words[0] . ' +' . $new_words[1];
            } else {
                $search_wordsH = $new_words[0];
            }


            $sql = 'SELECT document.id, link_id, parent_id, title, date_create, short_text, full_text, file, MATCH (`search_text`) AGAINST (:search) as `relev1`
                    FROM document, document_search
                    WHERE document.id=document_search.id AND link_id=:link_id AND (title LIKE :query OR MATCH (`search_text`) AGAINST (:search IN BOOLEAN MODE))
                    ORDER BY relev1 DESC';
            $bind = array('link_id' => $link->getId(), 'query' => '%' . $query . '%', 'search' => '+' . $search_words);


            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_Document::getInstanceByArray($res);
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


}
