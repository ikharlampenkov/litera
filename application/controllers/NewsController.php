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

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oNews->setLink($this->_link);
            $oNews->setTitle($data['title']);
            $oNews->setDatePublic($data['date']);
            $oNews->setShortText($data['short_text']);
            $oNews->setFullText($data['full_text']);

            if ($data['category_id'] != 'null') {
                $oNews->setCategory(SM_Module_NewsCategory::getInstanceById($data['category_id']));
            } else {
                $oNews->setCategory(null);
            }

            try {
                $oNews->insertToDb();
                if ($this->_category != null) {
                    $this->_redirect('/news/index/link/' . $this->_link->getLink() . '/categoryId/' . $this->_category->getId());
                } else {
                    $this->_redirect('/news/index/link/' . $this->_link->getLink());
                }
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('news', $oNews);
        $this->view->assign('categoryList', SM_Module_NewsCategory::getAllInstance());
    }

    public function editAction()
    {
        $oNews = SM_Module_News::getInstanceById($this->getRequest()->getParam('id'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oNews->setTitle($data['title']);
            $oNews->setDatePublic($data['date']);
            $oNews->setShortText($data['short_text']);
            $oNews->setFullText($data['full_text']);

            if ($data['category_id'] != 'null') {
                $oNews->setCategory(SM_Module_NewsCategory::getInstanceById($data['category_id']));
            } else {
                $oNews->setCategory(null);
            }

            try {
                $oNews->updateToDB();
                if ($this->_category != null) {
                    $this->_redirect('/news/index/link/' . $this->_link->getLink() . '/categoryId/' . $this->_category->getId());
                } else {
                    $this->_redirect('/news/index/link/' . $this->_link->getLink());
                }
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('news', $oNews);
        $this->view->assign('categoryList', SM_Module_NewsCategory::getAllInstance());
    }

    public function deleteAction()
    {
        $oNews = SM_Module_News::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oNews->deleteFromDB();
            $this->_redirect('/news/index/link/' . $this->_link->getLink());
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }

    public function addcategoryAction()
    {
        $oNewsCategory = new SM_Module_NewsCategory();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oNewsCategory->setTitle($data['title']);

            try {
                $oNewsCategory->insertToDb();
                $this->_redirect('/news/index/link/' . $this->_link->getLink());
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('newsCategory', $oNewsCategory);

    }

    public function editcategoryAction()
    {
        $oNewsCategory = SM_Module_NewsCategory::getInstanceById($this->getRequest()->getParam('categoryId'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oNewsCategory->setTitle($data['title']);

            try {
                $oNewsCategory->updateToDB();
                $this->_redirect('/news/index/link/' . $this->_link->getLink());
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('newsCategory', $oNewsCategory);
    }

    public function deletecategoryAction()
    {
        $oNewsCategory = SM_Module_NewsCategory::getInstanceById($this->getRequest()->getParam('categoryId'));
        try {
            $oNewsCategory->deleteFromDB();
            $this->_redirect('/news/index/link/' . $this->_link->getLink());
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }

}
