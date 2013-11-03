<?php

class MenuController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $menuId = $this->getRequest()->getParam('menuId', 0);
        $parentId = $this->getRequest()->getParam('parentId', null);

        $this->view->menuList = SM_Menu_Menu::getAllInstance();

        if ($parentId !== null) {
            $this->view->itemList = SM_Menu_Item::getAllInstance($parentId);
            $parentItem = SM_Menu_Item::getInstanceById($parentId);
        } else {
            if ($menuId != 0) {
                $this->view->itemList = SM_Menu_Item::getAllInstanceByMenu(SM_Menu_Menu::getInstanceById($menuId));
            } else {
                $this->view->itemList = SM_Menu_Item::getAllInstance(null);
            }
            $parentItem = null;
        }

        $this->view->menuId = $menuId;
        $this->view->parentItem = $parentItem;
    }

    public function addAction()
    {
        $parentId = $this->getRequest()->getParam('parentId', null);

        $oMenuItem = new SM_Menu_Item();
        if ($parentId != null) {
            $oMenuItem->setParent(SM_Menu_Item::getInstanceById($parentId));
        } else {
            $oMenuItem->setParent(null);
        }
        $oMenuItem->setHandler(SM_Menu_Handler::getInstanceById(1));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oMenuItem->setTitle($data['title']);
            $oMenuItem->setLink($data['title']);

            if (isset($data['is_visible'])) {
                $oMenuItem->setIsVisible(1);
            } else {
                $oMenuItem->setIsVisible(0);
            }

            $oMenuItem->setSortOrder($data['sort_order']);

            if ($data['parent_id'] != 'null') {
                $oParent = SM_Menu_Item::getInstanceById($data['parent_id']);
                $oMenuItem->setParent($oParent);
            } else {
                $oMenuItem->setParent(null);
            }

            $oHandler = SM_Menu_Handler::getInstanceById($data['handler_id']);
            $oMenuItem->setHandler($oHandler);

            if (isset($data['menu'])) {
                $oMenuItem->setMenuList($data['menu']);
            }

            try {
                $oMenuItem->insertToDb();

                if (isset($data['content_page'])) {
                    $oConPage = SM_Module_ContentPage::getInstanceByTitle($data['content_page']);
                    $oConPage->setLink($oMenuItem);
                    $oConPage->updateToDB();
                }

                if ($parentId != null) {
                    $this->_redirect('/menu/index/parentId/' . $parentId);
                } else {
                    $this->_redirect('/menu/');
                }
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('menuItem', $oMenuItem);
        $this->view->handlerList = SM_Menu_Handler::getAllInstance();
        $this->view->menuList = SM_Menu_Menu::getAllInstance();
        $this->view->parentList = SM_Menu_Item::getAllInstance(null);
    }

    public function editAction()
    {
        $parentId = $this->getRequest()->getParam('parentId', null);
        $oMenuItem = SM_Menu_Item::getInstanceById($this->getRequest()->getParam('id'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oMenuItem->setTitle($data['title']);
            $oMenuItem->setLink($data['link']);

            if (isset($data['is_visible'])) {
                $oMenuItem->setIsVisible(1);
            } else {
                $oMenuItem->setIsVisible(0);
            }

            $oMenuItem->setSortOrder($data['sort_order']);

            if ($data['parent_id'] != 'null') {
                $oParent = SM_Menu_Item::getInstanceById($data['parent_id']);
                $oMenuItem->setParent($oParent);
            } else {
                $oMenuItem->setParent(null);
            }

            $oHandler = SM_Menu_Handler::getInstanceById($data['handler_id']);
            $oMenuItem->setHandler($oHandler);

            if (isset($data['menu'])) {
                $oMenuItem->setMenuList($data['menu']);
            }

            try {
                $oMenuItem->updateToDb();

                if (isset($data['content_page'])) {
                    $oConPage = SM_Module_ContentPage::getInstanceByTitle($data['content_page']);
                    $oConPage->setLink($oMenuItem);
                    $oConPage->updateToDB();
                }

                if ($parentId != null) {
                    $this->_redirect('/menu/index/parentId/' . $parentId);
                } else {
                    $this->_redirect('/menu/');
                }
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('menuItem', $oMenuItem);
        $this->view->handlerList = SM_Menu_Handler::getAllInstance();
        $this->view->menuList = SM_Menu_Menu::getAllInstance();
        $this->view->parentList = SM_Menu_Item::getAllInstance(null);
    }

    public function deleteAction()
    {
        $parentId = $this->getRequest()->getParam('parentId', null);
        $oMenuItem = SM_Menu_Item::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oMenuItem->deleteFromDB();
            if ($parentId != null) {
                $this->_redirect('/menu/index/parentId/' . $parentId);
            } else {
                $this->_redirect('/menu/');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }

    public function addmenuAction()
    {
        $oMenuMenu = new SM_Menu_Menu();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oMenuMenu->setTitle($data['title']);
            $oMenuMenu->setCode($data['title']);

            try {
                $oMenuMenu->insertToDb();
                $this->_redirect('/menu/index/menuId/' . $oMenuMenu->getId());
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('menuMenu', $oMenuMenu);
    }

    public function editmenuAction()
    {
        $oMenuMenu = SM_Menu_Menu::getInstanceById($this->getRequest()->getParam('id'));
        $menuId = $this->getRequest()->getParam('menuId', 0);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oMenuMenu->setTitle($data['title']);
            $oMenuMenu->setCode($data['code']);

            try {
                $oMenuMenu->updateToDb();
                if ($menuId != 0) {
                    $this->_redirect('/menu/index/menuId/' . $menuId);
                } else {
                    $this->_redirect('/menu/');
                }
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('menuMenu', $oMenuMenu);
    }

    public function deletemenuAction()
    {
        $oMenuMenu = SM_Menu_Menu::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oMenuMenu->deleteFromDB();
            $this->_redirect('/menu/');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }

    public function contentpagelistAction()
    {
        if ($this->getRequest()->isPost()) {

            $this->_helper->layout()->disableLayout();
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');

            $item = $this->getRequest()->getParam('item', 0);
            $contentPageList = SM_Module_ContentPage::getAllInstance();

            $html = $this->view->partial(
                "/menu/_elements/contentpagelist.phtml",
                array('contentPageList' => $contentPageList, 'item' => $item)
            );

            $json = Zend_Json::encode(array('html' => $html));

            $this->getResponse()->setBody($json)->setHeader(
                "content-type",
                "application/json", true
            );
        }
    }


}

