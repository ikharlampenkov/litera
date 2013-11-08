<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 23.12.11
 * Time: 21:36
 * To change this template use File | Settings | File Templates.
 */

/*
 *
CREATE TABLE IF NOT EXISTS `content_page` (
  `link_id` int(10) unsigned NOT NULL,
  `page_title` varchar(255) NOT NULL COMMENT 'английское название для системы',
  `parent_page` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  PRIMARY KEY (`page_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 */

class SM_Module_ContentPage
{
    /**
     * @var SM_Menu_Item
     */
    protected $_link = null;

    /**
     * @var string
     */
    protected $_pageTitle;

    /**
     * @var SM_Module_ContentPage
     */
    protected $_parentPage = null;

    /**
     * @var string
     */
    protected $_title;

    /**
     * @var string
     */
    protected $_content;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_content; //str_replace('&quot;', "", stripcslashes($this->_content));
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->_pageTitle;
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
     * @param \SM_Module_ContentPage $parentPage
     */
    public function setParentPage($parentPage)
    {
        $this->_parentPage = $parentPage;
    }

    /**
     * @return \SM_Module_ContentPage
     */
    public function getParentPage()
    {
        return $this->_parentPage;
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
     * @param SM_Module_ContentPage|null $value
     *
     * @return null
     */
    protected function _prepareNull($value)
    {
        if (is_null($value) || empty($value)) {
            return null;
        } else {
            return $value->getPageTitle();
        }
    }

    /**
     * @param SM_Menu_Item|null $value
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
        $this->_db = Zend_Registry::get('db');
    }

    public function insertToDB()
    {
        try {
            $sql
                = 'INSERT INTO content_page(link_id, page_title, parent_page, title, content)
                            VALUES(:link_id, :page_title, :parent_page, :title, :content)';
            $this->_db->query($sql, array('link_id' => $this->_prepareNullLink($this->_link), 'page_title' => $this->_pageTitle, 'parent_page' => $this->_prepareNull($this->_parentPage), 'title' => $this->_title, 'content' => $this->_content));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //link_id,  page_title, parent_page, title, content

    public function updateToDB()
    {
        try {
            $sql
                = 'UPDATE content_page
                       SET link_id=:link_id, parent_page=:parent_page, title=:title, content=:content
                    WHERE page_title=:page_title';
            $this->_db->query($sql, array('link_id' => $this->_prepareNullLink($this->_link), 'page_title' => $this->_pageTitle, 'parent_page' => $this->_prepareNull($this->_parentPage), 'title' => $this->_title, 'content' => $this->_content));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM content_page WHERE page_title=:page_title';
            $this->_db->query($sql, array('page_title' => $this->_pageTitle));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param string $parentPage
     *
     * @throws Exception
     * @return array|bool
     */
    public static function getAllInstance($parentPage = '')
    {
        try {
            $db = Zend_Registry::get('db');

            $sql = 'SELECT * FROM content_page ';
            $bind = array();

            if ($parentPage == null) {
                $sql .= ' WHERE parent_page IS NULL ';
            } elseif (!empty($parentPage)) {
                $sql .= ' WHERE parent_page=:parent_page ';
                $bind = array('parent_page' => $parentPage);
            }

            $result = $db->query($sql, $bind)->fetchAll();


            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_ContentPage::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            StdLib_Log::logMsg($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_ContentPage();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            StdLib_Log::logMsg($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param $title
     *
     * @return bool|SM_Module_ContentPage
     * @throws Exception
     */
    public static function getInstanceByTitle($title)
    {
        try {
            $sql = 'SELECT * FROM content_page WHERE page_title=:page_title';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('page_title' => $title))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_ContentPage();
                $o->fillFromArray($result[0]);
                return $o;
            } else {
                return false;
            }
        } catch (Exception $e) {
            StdLib_Log::logMsg($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param SM_Menu_Item $link
     *
     * @return bool|SM_Module_ContentPage
     * @throws Exception
     */
    public static function getInstanceByLink($link)
    {
        try {
            $sql = 'SELECT * FROM content_page WHERE link_id=:link';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('link' => $link->getId()))->fetch();

            if (!empty($result)) {
                $o = new SM_Module_ContentPage();
                $o->fillFromArray($result);
                return $o;
            } else {
                return false;
            }
        } catch (Exception $e) {
            StdLib_Log::logMsg($e->getMessage());
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
        $this->setPageTitle($values['page_title']);
        $this->setTitle($values['title']);
        $this->setContent($values['content']);

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        if ($oMenuItem != false) {
            $this->setLink($oMenuItem);
        }

        $oConPage = SM_Module_ContentPage::getInstanceByTitle($values['parent_page']);
        if ($oConPage !== false) {
            $this->setParentPage($oConPage);
        }
    }

    public function getChild()
    {
        return self::getAllInstance($this->_pageTitle);
    }

    /**
     * Позволяет оперделить наличие потомков
     *
     * @return bool
     * @throws Exception
     */
    public function hasChild()
    {
        try {
            $sql = 'SELECT COUNT(page_title) AS child_cnt FROM content_page WHERE parent_page=:parent_page';
            $result = $this->_db->query($sql, array('parent_page' => $this->_pageTitle))->fetch();

            if (isset($result['child_cnt']) && $result['child_cnt'] > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            StdLib_Log::logMsg($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

}
