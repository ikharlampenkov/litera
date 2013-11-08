<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 25.05.12
 * Time: 23:16
 * To change this template use File | Settings | File Templates.
 */
class NewsController extends Zend_Controller_Action
{
    /**
     * @var SM_Menu_Item
     */
    protected $_link;

    /**
     * @var SM_Module_NewsCategory|null
     */
    protected $_category = null;

    public function init()
    {
        $this->_link = SM_Menu_Item::getInstanceByLink($this->getRequest()->getParam('link'));
        $this->view->assign('link', $this->_link->getLink());
        $this->view->assign('linkInfo', $this->_link);
        $this->view->assign('pathway', $this->_link->getPathWay());

        $categoryId = $this->getRequest()->getParam('categoryId', '');
        if ($categoryId != '' && $categoryId != 0) {
            $this->_category = SM_Module_NewsCategory::getInstanceById($categoryId);
        }

        $this->view->assign('category', $this->_category);

        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->assign('newsList', SM_Module_News::getAllInstance($this->_link, $this->_category));
        $this->view->assign('categoryList', SM_Module_NewsCategory::getAllInstance());
    }

    public function viewnewsAction()
    {
        $oNews = SM_Module_News::getInstanceById($this->getRequest()->getParam('id'));
        $this->view->assign('news', $oNews);

        $this->view->assign('categoryList', SM_Module_NewsCategory::getAllInstance());
    }

    public function viewAction()
    {
        $this->view->assign('newsList', SM_Module_News::getAllInstance($this->_link, $this->_category));
        $this->view->assign('categoryList', SM_Module_NewsCategory::getAllInstance());
    }

    public function addAction()
    {
        $oNews = new SM_Module_News();
        $oNews->setCategory($this->_category);

        $form = new Application_Form_News_News();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'news', 'action' => 'add')));
        $form->getElement('cancel')->setHref('/news/index/link/' . $this->_link->getLink());
        $form->submit->setLabel('Добавить');

        $form->setCategoryList(SM_Module_NewsCategory::getAllInstance());
        if ($this->_category != null) {
            $form->setDefault('category', $this->_category->getId());
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oNews->setLink($this->_link);
                $oNews->setTitle($form->getValue('title'));
                $oNews->setDatePublic($form->getValue('date'));
                $oNews->setShortText($form->getValue('short_text'));
                $oNews->setFullText($form->getValue('full_text'));

                if ($form->getValue('category') != 'null') {
                    $oNews->setCategory(SM_Module_NewsCategory::getInstanceById($form->getValue('category')));
                } else {
                    $oNews->setCategory(null);
                }

                try {
                    $oNews->insertToDb();
                    if ($this->_category != null) {
                        $this->redirect('/news/index/link/' . $this->_link->getLink() . '/categoryId/' . $this->_category->getId());
                    } else {
                        $this->redirect('/news/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;
        //$this->view->assign('news', $oNews);
    }

    public function editAction()
    {
        $oNews = SM_Module_News::getInstanceById($this->getRequest()->getParam('id'));

        $form = new Application_Form_News_News();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'news', 'action' => 'edit', 'id' => $oNews->getId())));
        $form->getElement('cancel')->setHref('/news/index/link/' . $this->_link->getLink());
        $form->submit->setLabel('Сохранить');

        $form->setDefault('title', $oNews->getTitle());
        $form->setDefault('date', $oNews->getDatePublic());
        $form->setDefault('short_text', $oNews->getShortText());
        $form->setDefault('full_text', $oNews->getFullText());

        if ($oNews->getFile()->getName() != '') {
            $form->setImage($oNews->getFile()->getSubPath() . '/' . $oNews->getFile()->getName(), $oNews->getTitle());
        }

        $form->setCategoryList(SM_Module_NewsCategory::getAllInstance());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oNews->setTitle($form->getValue('title'));
                $oNews->setDatePublic($form->getValue('date'));
                $oNews->setShortText($form->getValue('short_text'));
                $oNews->setFullText($form->getValue('full_text'));

                if ($form->getValue('category') != 'null') {
                    $oNews->setCategory(SM_Module_NewsCategory::getInstanceById($form->getValue('category')));
                } else {
                    $oNews->setCategory(null);
                }
                try {
                    $oNews->updateToDB();
                    if ($this->_category != null) {
                        $this->redirect('/news/index/link/' . $this->_link->getLink() . '/categoryId/' . $this->_category->getId());
                    } else {
                        $this->redirect('/news/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }

        }

        $this->view->form = $form;
        //$this->view->assign('news', $oNews);
        //$this->view->assign('categoryList', SM_Module_NewsCategory::getAllInstance());
    }

    public function deleteAction()
    {
        $oNews = SM_Module_News::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oNews->deleteFromDB();
            $this->redirect('/news/index/link/' . $this->_link->getLink());
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }

    public function addcategoryAction()
    {
        $oNewsCategory = new SM_Module_NewsCategory();

        $form = new Application_Form_News_Category();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'news', 'action' => 'addcategory', 'link' => $this->_link->getLink())));
        $form->getElement('cancel')->setHref('/news/index/link/' . $this->_link->getLink());
        $form->submit->setLabel('Добавить');

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oNewsCategory->setTitle($form->getValue('title'));

                try {
                    $oNewsCategory->insertToDb();
                    $this->redirect('/news/index/link/' . $this->_link->getLink());
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;
        //$this->view->assign('newsCategory', $oNewsCategory);
    }

    public function editcategoryAction()
    {
        $oNewsCategory = SM_Module_NewsCategory::getInstanceById($this->getRequest()->getParam('categoryId'));

        $form = new Application_Form_News_Category();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'news', 'action' => 'editcategory', 'categoryId' => $oNewsCategory->getId(), 'link' => $this->_link->getLink())));
        $form->getElement('cancel')->setHref('/news/index/link/' . $this->_link->getLink());
        $form->submit->setLabel('Сохранить');

        $form->setDefault('title', $oNewsCategory->getTitle());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oNewsCategory->setTitle($form->getValue('title'));

                try {
                    $oNewsCategory->updateToDB();
                    $this->redirect('/news/index/link/' . $this->_link->getLink());
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;
        //$this->view->assign('newsCategory', $oNewsCategory);
    }

    public function deletecategoryAction()
    {
        $oNewsCategory = SM_Module_NewsCategory::getInstanceById($this->getRequest()->getParam('categoryId'));
        try {
            $oNewsCategory->deleteFromDB();
            $this->redirect('/news/index/link/' . $this->_link->getLink());
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }

}
