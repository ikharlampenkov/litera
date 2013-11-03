<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * @var SM_Menu_Item
     */
    protected $_link;

    public function init()
    {
        /*
        $this->_link = SM_Menu_Item::getInstanceByLink('main_page');
        $this->view->assign('link', $this->_link->getLink());

        $this->view->assign('linkInfo', $this->_link);
        $this->view->assign('pathway', $this->_link->getPathWay());
        */
    }

    public function indexAction()
    {

    }
}

