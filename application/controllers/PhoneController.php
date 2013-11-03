<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 25.05.12
 * Time: 23:16
 * To change this template use File | Settings | File Templates.
 */
class PhoneController extends Zend_Controller_Action
{
    /**
     * @var SM_Menu_Item
     */
    protected $_link;

    public function init()
    {
        /*
        $this->_link = SM_Menu_Item::getInstanceByLink($this->getRequest()->getParam('link'));
        if ($this->_link == false) {
            $this->_link = SM_Menu_Group::getInstanceByLink($this->getRequest()->getParam('link'));
        }
        $this->view->assign('link', $this->_link->getLink());

        $this->view->assign('linkInfo', $this->_link);
        $this->view->assign('pathway', $this->_link->getPathWay());
        */
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $oPhone = SM_Module_Phone::getInstanceById(1);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oPhone->setAddress($data['address']);
            $oPhone->setPhone($data['phone']);
            $oPhone->setPostIndex($data['post_index']);

            try {
                $oPhone->updateToDB();
                $this->_redirect('/phone/');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('phone', $oPhone);
    }

}
