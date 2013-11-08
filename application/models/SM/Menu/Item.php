<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 21.05.12
 * Time: 23:15
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `menu_item` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL,
   `link` varchar(255) NOT NULL,
   `parent_id` int(10) unsigned NOT NULL,
   `handler_id` int(10) unsigned NOT NULL,
   PRIMARY KEY (`id`),
   KEY `parent_id` (`parent_id`,`handler_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */

/*
 * menu_menu_item
 * menu_id
 * item_id
 */

class SM_Menu_Item
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
    protected $_link = '';

    /**
     * @var SM_Menu_Item
     */
    protected $_parent = null;

    /**
     * @var SM_Menu_Handler
     */
    protected $_handler;

    /**
     * @var bool
     */
    protected $_isVisible = 0;

    /**
     * @var int порядок сортировки
     */
    protected $_sortOrder = 1;


    /**
     * @var array
     */
    protected $_menuList = array();

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;


    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->_link = mb_convert_case(StdLib_Functions::translitIt($link), MB_CASE_LOWER, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->_link;
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

    /**
     * @param \SM_Menu_Item $parent
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }

    /**
     * @return \SM_Menu_Menu
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * @param \SM_Menu_Handler $handler
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
    }

    /**
     * @return \SM_Menu_Handler
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * @param boolean $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->_isVisible = $isVisible;
    }

    /**
     * @return boolean
     */
    public function getIsVisible()
    {
        return $this->_isVisible;
    }

    /**
     * @param $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->_sortOrder = $sortOrder;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_sortOrder;
    }

    /**
     * @param SM_Menu_Item|null $value
     *
     * @return null|int
     */
    protected function _prepareNull($value)
    {
        if (is_null($value) || empty($value)) {
            return null;
        } else {
            return $value->getId();
        }

    }

    /**
     * Функция возвращает url для страницы
     *
     * @param $mode режим пользователя, его роль admin guest
     *
     * @return string
     */
    public function getUrl($mode)
    {
        $tempURL = '/';
        if ($mode == 'guest') {
            $tempURL .= $this->getFullUrl() . '/';
        } elseif ($mode == 'admin') {
            if ($this->_handler->getController() == 'Contentpage') {
                $tempURL .= $this->_handler->getController() . '/edit/title/' . $this->_link . '/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'Document') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'News') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'Calendar') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'Vote') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'Partners') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'GuestBook') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } elseif ($this->_handler->getController() == 'PaymentPoint') {
                $tempURL .= $this->_handler->getController() . '/index/link/' . $this->_link . '/';
            } else {
                $tempURL .= $this->getFullUrl() . '/';
            }

        }
        return $tempURL;
    }

    public function getFullUrl($delimiter = '/')
    {
        if ($this->_parent !== null) {
            return $this->_parent->getFullUrl($delimiter) . $delimiter . $this->_link;
        } else {
            return $this->_link;
        }

    }


    public function getRoute(Zend_Controller_Router_Rewrite &$router)
    {
        $defaults = array();
        $requirements = array();

        if ($this->_handler->getController() == 'Contentpage') {
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'view';
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            );
            $router->addRoute($this->getFullUrl('-'), $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/subtitle/:subtitle/',
                array('controller' => $this->_handler->getController(), 'action' => 'view', 'link' => $this->_link), array('subtitle' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-subtitle', $route);
        } elseif ($this->_handler->getController() == 'Document') {
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'view';
            $defaults['parent'] = 0;
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            ); //'/' . $this->_parent->getLink() . '/' . $this->_link . '/'
            $router->addRoute($this->getFullUrl('-'), $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/parent/:parent/',
                array('controller' => $this->_handler->getController(), 'action' => 'view', 'link' => $this->_link), array('parent' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-parent', $route);


            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/viewdoc/:id/parent/:parent/',
                array('controller' => $this->_handler->getController(), 'action' => 'viewdoc', 'link' => $this->_link), array('id' => '[\w\-]+', 'parent' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-viewdoc', $route);
        } elseif ($this->_handler->getController() == 'News') {
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'view';
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            );
            $router->addRoute($this->getFullUrl('-'), $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/categoryId/:categoryId/',
                array('controller' => $this->_handler->getController(), 'action' => 'view', 'link' => $this->_link), array('categoryId' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-category', $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/viewnews/:id/',
                array('controller' => $this->_handler->getController(), 'action' => 'viewnews', 'link' => $this->_link), array('id' => '[\d]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-viewnews', $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/viewnews/:id/categoryId/:categoryId/',
                array('controller' => $this->_handler->getController(), 'action' => 'viewnews', 'link' => $this->_link), array('id' => '[\d]+', 'categoryId' => '[\d]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-viewnews-category', $route);
        } elseif ($this->_handler->getController() == 'Calendar') {
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'view';
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            );
            $router->addRoute($this->getFullUrl('-'), $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/categoryId/:categoryId/',
                array('controller' => $this->_handler->getController(), 'action' => 'view', 'link' => $this->_link), array('categoryId' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-category', $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/viewcalendar/:id/',
                array('controller' => $this->_handler->getController(), 'action' => 'viewcalendar', 'link' => $this->_link), array('id' => '[\d]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-viewcalendar', $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/viewcalendar/:id/categoryId/:categoryId/',
                array('controller' => $this->_handler->getController(), 'action' => 'viewcalendar', 'link' => $this->_link), array('id' => '[\d]+', 'categoryId' => '[\d]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-viewcalendar-category', $route);
        } elseif ($this->_handler->getController() == 'Vote') {
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'sendmsg';
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            );
            $router->addRoute($this->getFullUrl('-'), $route);
        } elseif ($this->_handler->getController() == 'GuestBook') {
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'view';
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            );
            $router->addRoute($this->getFullUrl('-'), $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/parent/:parent/',
                array('controller' => $this->_handler->getController(), 'action' => 'view', 'link' => $this->_link), array('parent' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-parent', $route);


            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/view-question/:id/parent/:parent/',
                array('controller' => $this->_handler->getController(), 'action' => 'view-question', 'link' => $this->_link), array('id' => '[\w\-]+', 'parent' => '[\w\-]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-view-question', $route);
        } elseif ($this->_handler->getController() == 'PaymentPoint') {
            //PaymentPoint
            $defaults['controller'] = $this->_handler->getController();
            $defaults['action'] = 'view';
            $defaults['link'] = $this->_link;

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/',
                $defaults, $requirements
            );
            $router->addRoute($this->getFullUrl('-'), $route);

            $route = new Zend_Controller_Router_Route(
                '/' . $this->getFullUrl() . '/viewpoint/:id/',
                array('controller' => $this->_handler->getController(), 'action' => 'viewpoint', 'link' => $this->_link), array('id' => '[\d]+')
            );
            $router->addRoute($this->getFullUrl('-') . '-viewnews', $route);
        } else {

        }
    }

    public function getPathWay()
    {
        if ($this->_parent !== null) {
            return array(0 => array('link' => $this->_parent->getLink(), 'title' => $this->_parent->getTitle()));
        } else {
            return array(0 => array('link' => '', 'title' => ''));
        }
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
            $sql
                = 'INSERT INTO menu_item(title, link, parent_id, handler_id, is_visible, sort_order)
                        VALUES (:title, :link, :parent_id, :handler_id, :is_visible, :sort_order)';
            $this->_db->query(
                $sql, array('title' => $this->_title, 'link' => $this->_link, 'parent_id' => $this->_prepareNull($this->_parent),
                    'handler_id' => $this->_handler->getId(), 'is_visible' => (int)$this->_isVisible, 'sort_order' => $this->_sortOrder)
            );

            $this->_id = $this->_db->lastInsertId('menu_item', 'id');

            $this->updateMenuList();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateToDb()
    {
        try {
            $sql
                = 'UPDATE menu_item
                       SET title=:title, link=:link, parent_id=:parent_id, handler_id=:handler_id, is_visible=:is_visible, sort_order=:sort_order
                     WHERE id=:id';
            $this->_db->query(
                $sql, array('id' => $this->_id, 'title' => $this->_title, 'link' => $this->_link, 'parent_id' => $this->_prepareNull($this->_parent),
                    'handler_id' => $this->_handler->getId(), 'is_visible' => (int)$this->_isVisible, 'sort_order' => $this->_sortOrder)
            );
            $this->updateMenuList();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function deleteFromDb()
    {
        try {
            $sql = 'DELETE FROM menu_menu_item WHERE item_id=:item';
            $this->_db->query($sql, array('item' => $this->_id));

            $sql = 'DELETE FROM menu_item WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * @static
     *
     * @param $values
     *
     * @return SM_Menu_Handler
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Menu_Item();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     *
     * @param int|null $parent
     *
     * @throws Exception
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance($parent)
    {
        try {
            $db = Zend_Registry::get('db');

            $sql = 'SELECT * FROM menu_item';

            if ($parent != null) {
                $sql .= ' WHERE parent_id=:parent_id ORDER BY sort_order';
                $result = $db->query($sql, array('parent_id' => $parent))->fetchAll();
            } else {
                $sql .= ' WHERE parent_id IS NULL ORDER BY sort_order';
                $result = $db->query($sql)->fetchAll();
            }

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Menu_Item::getInstanceByArray($res);
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
     * @param SM_Menu_Menu $menu
     *
     * @return array|bool
     * @throws Exception
     */
    public static function getAllInstanceByMenu($menu)
    {
        try {
            $sql = 'SELECT * FROM menu_item JOIN menu_menu_item ON menu_item.id=menu_menu_item.item_id WHERE parent_id IS NULL AND menu_menu_item.menu_id=:menu_id ORDER BY sort_order';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('menu_id' => $menu->getId()))->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Menu_Item::getInstanceByArray($res);
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
     *
     * @param $id
     *
     * @return bool|SM_Menu_Item
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM menu_item WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => (int)$id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Menu_Item();
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
     *
     * @param $link
     *
     * @return bool|SM_Menu_Item
     * @throws Exception
     */
    public static function getInstanceByLink($link)
    {
        try {
            $sql = 'SELECT * FROM menu_item WHERE REPLACE(link, "_", "")=:link OR link=:link';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('link' => $link))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Menu_Item();
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
        $this->setLink($values['link']);

        $oParent = SM_Menu_Item::getInstanceById($values['parent_id']);
        if ($oParent !== false) {
            $this->setParent($oParent);
        }

        $oHandler = SM_Menu_Handler::getInstanceById($values['handler_id']);
        $this->setHandler($oHandler);

        $this->setIsVisible($values['is_visible']);
        $this->setSortOrder($values['sort_order']);
        $this->extractMenuList();
    }

    public function setMenuList($data)
    {
        $this->_menuList = array();
        foreach ($data as $menuId) {
            $this->_menuList[] = $menuId;
        }
    }

    public function getMenuList()
    {
        return $this->_menuList;
    }

    public function checkMenu($id)
    {
        return in_array($id, $this->_menuList);
    }

    protected function updateMenuList()
    {
        $sql = 'DELETE FROM menu_menu_item WHERE item_id=:item';
        $this->_db->query($sql, array('item' => $this->_id));

        $sql = 'INSERT INTO menu_menu_item(menu_id, item_id) VALUES(:menu, :item)';
        foreach ($this->_menuList as $menu) {
            $this->_db->query($sql, array('menu' => $menu, 'item' => $this->_id));
        }
    }

    protected function extractMenuList()
    {
        $sql = 'SELECT * FROM menu_menu_item WHERE item_id=:item';
        $result = $this->_db->query($sql, array('item' => $this->_id))->fetchAll();

        if (isset($result[0])) {
            foreach ($result as $res) {
                $this->_menuList[] = $res['menu_id'];
            }
        }
    }

    public function getChild()
    {
        return self::getAllInstance($this->_id);
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
            $sql = 'SELECT COUNT(id) AS child_cnt FROM menu_item WHERE parent_id=:parent_id';
            $result = $this->_db->query($sql, array('parent_id' => $this->_id))->fetch();

            if (isset($result['child_cnt']) && $result['child_cnt'] > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}