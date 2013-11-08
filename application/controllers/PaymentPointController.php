<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 25.05.12
 * Time: 23:16
 * To change this template use File | Settings | File Templates.
 */
class PaymentPointController extends Zend_Controller_Action
{
    /**
     * @var SM_Menu_Item
     */
    protected $_link;

    public function init()
    {
        $this->_link = SM_Menu_Item::getInstanceByLink($this->getRequest()->getParam('link'));
        $this->view->assign('link', $this->_link->getLink());
        $this->view->assign('linkInfo', $this->_link);
        $this->view->assign('pathway', $this->_link->getPathWay());

        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->assign('pointList', SM_Module_PaymentPoint::getAllInstance($this->_link, $this->_category));
    }

    public function viewpointAction()
    {
        $oPaymentPoint = SM_Module_PaymentPoint::getInstanceById($this->getRequest()->getParam('id'));
        $this->view->assign('point', $oPaymentPoint);
    }

    public function viewAction()
    {
        $this->view->assign('pointList', SM_Module_PaymentPoint::getAllInstance($this->_link));
    }

    public function addAction()
    {
        $oPaymentPoint = new SM_Module_PaymentPoint();


        $form = new Application_Form_PaymentPoint_PaymentPoint();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'PaymentPoint', 'action' => 'add')));
        $form->getElement('cancel')->setHref('/PaymentPoint/index/link/' . $this->_link->getLink());
        $form->submit->setLabel('Добавить');

        $form->setCategoryList(SM_Module_PaymentPointCategory::getAllInstance());
        if ($this->_category != null) {
            $form->setDefault('category', $this->_category->getId());
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oPaymentPoint->setLink($this->_link);
                $oPaymentPoint->setTitle($form->getValue('title'));
                $oPaymentPoint->setDatePublic($form->getValue('date'));
                $oPaymentPoint->setShortText($form->getValue('short_text'));
                $oPaymentPoint->setFullText($form->getValue('full_text'));

                if ($form->getValue('category') != 'null') {
                    $oPaymentPoint->setCategory(SM_Module_PaymentPointCategory::getInstanceById($form->getValue('category')));
                } else {
                    $oPaymentPoint->setCategory(null);
                }

                try {
                    $oPaymentPoint->insertToDb();
                    if ($this->_category != null) {
                        $this->redirect('/PaymentPoint/index/link/' . $this->_link->getLink() . '/categoryId/' . $this->_category->getId());
                    } else {
                        $this->redirect('/PaymentPoint/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $oPaymentPoint = SM_Module_PaymentPoint::getInstanceById($this->getRequest()->getParam('id'));

        $form = new Application_Form_PaymentPoint_PaymentPoint();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'PaymentPoint', 'action' => 'edit', 'id' => $oPaymentPoint->getId())));
        $form->getElement('cancel')->setHref('/PaymentPoint/index/link/' . $this->_link->getLink());
        $form->submit->setLabel('Сохранить');

        $form->setDefault('title', $oPaymentPoint->getTitle());
        $form->setDefault('date', $oPaymentPoint->getDatePublic());
        $form->setDefault('short_text', $oPaymentPoint->getShortText());
        $form->setDefault('full_text', $oPaymentPoint->getFullText());

        if ($oPaymentPoint->getFile()->getName() != '') {
            $form->setImage($oPaymentPoint->getFile()->getSubPath() . '/' . $oPaymentPoint->getFile()->getName(), $oPaymentPoint->getTitle());
        }

        $form->setCategoryList(SM_Module_PaymentPointCategory::getAllInstance());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oPaymentPoint->setTitle($form->getValue('title'));
                $oPaymentPoint->setDatePublic($form->getValue('date'));
                $oPaymentPoint->setShortText($form->getValue('short_text'));
                $oPaymentPoint->setFullText($form->getValue('full_text'));

                if ($form->getValue('category') != 'null') {
                    $oPaymentPoint->setCategory(SM_Module_PaymentPointCategory::getInstanceById($form->getValue('category')));
                } else {
                    $oPaymentPoint->setCategory(null);
                }
                try {
                    $oPaymentPoint->updateToDB();
                    if ($this->_category != null) {
                        $this->redirect('/PaymentPoint/index/link/' . $this->_link->getLink() . '/categoryId/' . $this->_category->getId());
                    } else {
                        $this->redirect('/PaymentPoint/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }

        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $oPaymentPoint = SM_Module_PaymentPoint::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oPaymentPoint->deleteFromDB();
            $this->redirect('/PaymentPoint/index/link/' . $this->_link->getLink());
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }
}
