<?php

class GuestBookController extends Zend_Controller_Action
{

    /**
     * @var SM_Menu_Item
     */
    protected $_link;

    /**
     * @var SM_Module_GuestBook|null
     */
    protected $_parent = null;


    public function init()
    {
        $this->_link = SM_Menu_Item::getInstanceByLink($this->getRequest()->getParam('link'));

        $this->view->assign('link', $this->_link->getLink());
        $this->view->assign('linkInfo', $this->_link);
        $this->view->assign('pathway', $this->_link->getPathWay());

        $parent = $this->getRequest()->getParam('parent', '');
        if (!empty($parent)) {
            $this->_parent = SM_Module_GuestBook::getInstanceById($parent);
        }
        $this->view->assign('parentItem', $this->_parent);
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //$this->view->assign('folderList', SM_Module_GuestBook::getFolderList($this->_link, $this->_parent));
        $this->view->assign('questionList', SM_Module_GuestBook::getAllInstance($this->_link, $this->_parent));
    }

    public function viewQuestionAction()
    {
        $oQuestion = SM_Module_GuestBook::getInstanceById($this->getRequest()->getParam('id'));
        $this->view->assign('question', $oQuestion);
    }

    public function viewAction()
    {
        $oQuestion = new SM_Module_GuestBook();
        $oQuestion->setLink($this->_link);
        $oQuestion->setParent($this->_parent);

        //Инициализируем форму
        $form = new Application_Form_GuestBook_Question();
        $helperUrl = new Zend_View_Helper_Url();
        if ($this->_parent != null) {
            $form->setAction($helperUrl->url(array('controller' => 'guest-book', 'action' => 'index', 'parent' => $this->_parent->getId()), $this->_link->getFullUrl('-') . '-parent'));
        } else {
            $form->setAction($helperUrl->url(array('controller' => 'guest-book', 'action' => 'index'), $this->_link->getFullUrl('-')));
        }
        $form->removeElement('cancel');
        $form->removeElement('moderate');
        $form->removeElement('answer');
        $form->submit->setLabel('Отправить вопрос');
        $form->email->setRequired(true);
        $form->name->setRequired(true);
        $form->setParentList(SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));
        if ($this->_parent != null) {
            $form->setDefault('parent', $oQuestion->getParent()->getId());
        }
        $mainSession = new Zend_Session_Namespace('guestBook');

        if (!isset($mainSession->isComplite)) {
            $mainSession->isComplite = false;
        }
        $this->view->assign('openForm', false);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oQuestion->setQuestion($form->getValue('question'));
                $oQuestion->setParent(SM_Module_GuestBook::getInstanceById($form->getValue('parent')));
                $oQuestion->setSubject($form->getValue('subject'));
                $oQuestion->setName($form->getValue('name'));
                $oQuestion->setEmail($form->getValue('email'));

                $oQuestion->setIsModerate(false);
                $oQuestion->setIsFolder(false);

                try {
                    $oQuestion->insertToDB();
                    $mainSession->isComplite = true;
                    $this->_redirect($helperUrl->url(array('controller' => 'guest-book', 'action' => 'index', 'parent' => $this->_parent->getId()), $this->_link->getFullUrl('-') . '-parent'));
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            } else {
                $this->view->assign('openForm', true);
            }
        }

        $this->view->assign('isComplite', $mainSession->isComplite);

        if ($mainSession->isComplite == true) {
            $mainSession->isComplite = false;
        }

        $this->view->assign('questionList', SM_Module_GuestBook::getAllInstance($this->_link, $this->_parent, SM_Module_GuestBook::IS_MODERATE));
        $this->view->form = $form;
    }

    public function addAction()
    {
        $oQuestion = new SM_Module_GuestBook();
        $oQuestion->setLink($this->_link);
        $oQuestion->setParent($this->_parent);

        //Инициализируем форму
        $form = new Application_Form_GuestBook_Question();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'guest-book', 'action' => 'add')));
        if ($this->_parent != null) {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
        } else {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink());
        }
        $form->submit->setLabel('Добавить');

        $form->setParentList(SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));
        $form->setDefault('parent', $oQuestion->getParent()->getId());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oQuestion->setQuestion($form->getValue('question'));
                $oQuestion->setAnswer($form->getValue('answer'));
                $oQuestion->setParent(SM_Module_GuestBook::getInstanceById($form->getValue('parent')));
                $oQuestion->setSubject($form->getValue('subject'));
                $oQuestion->setName($form->getValue('name'));
                $oQuestion->setEmail($form->getValue('email'));

                if ($form->getValue('moderate') == '1') {
                    $oQuestion->setIsModerate(true);
                } else {
                    $oQuestion->setIsModerate(0);
                }
                $oQuestion->setIsFolder(0);

                try {
                    $oQuestion->insertToDb();
                    if ($this->_parent != null) {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
                    } else {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;

        //$this->view->assign('question', $oQuestion);
        //$this->view->assign('folderList', SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));
    }

    public function editAction()
    {
        $oQuestion = SM_Module_GuestBook::getInstanceById($this->getRequest()->getParam('id'));

        $form = new Application_Form_GuestBook_Question();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'guest-book', 'action' => 'edit', 'id' => $oQuestion->getId())));
        if ($this->_parent != null) {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
        } else {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink());
        }
        $form->submit->setLabel('Сохранить');

        $form->setParentList(SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));

        $form->setDefault('question', $oQuestion->getQuestion());
        $form->setDefault('parent', $oQuestion->getParent()->getId());

        $form->setDefault('answer', $oQuestion->getAnswer());
        $form->setDefault('subject', $oQuestion->getSubject());
        $form->setDefault('name', $oQuestion->getName());
        $form->setDefault('email', $oQuestion->getEmail());
        $form->setDefault('isModerate', $oQuestion->getIsModerate());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oQuestion->setQuestion($form->getValue('question'));
                $oQuestion->setAnswer($form->getValue('answer'));
                $oQuestion->setParent(SM_Module_GuestBook::getInstanceById($form->getValue('parent')));
                $oQuestion->setSubject($form->getValue('subject'));
                $oQuestion->setName($form->getValue('name'));
                $oQuestion->setEmail($form->getValue('email'));
                if ($form->getValue('moderate') == '1') {
                    $oQuestion->setIsModerate(true);
                } else {
                    $oQuestion->setIsModerate(0);
                }
                $oQuestion->setIsFolder(0);

                try {
                    $oQuestion->updateToDB();
                    if ($this->_parent != null) {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
                    } else {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;

        //$this->view->assign('question', $oQuestion);
        //$this->view->assign('folderList', SM_Module_GuestBook::getFolderList($this->_link, $this->_parent));
    }

    public function deleteAction()
    {
        $oQuestion = SM_Module_GuestBook::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oQuestion->deleteFromDB();
            if ($this->_parent != null) {
                $this->_redirect('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
            } else {
                $this->_redirect('/guest-book/index/link/' . $this->_link->getLink());
            }
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }

    public function addFolderAction()
    {
        $oQuestion = new SM_Module_GuestBook();
        $oQuestion->setLink($this->_link);
        $oQuestion->setParent($this->_parent);

        //Инициализируем форму
        $form = new Application_Form_GuestBook_Folder();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'guest-book', 'action' => 'add-folder')));
        if ($this->_parent != null) {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
        } else {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink());
        }
        $form->submit->setLabel('Добавить');

        $form->setParentList(SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));
        if ($this->_parent !== null) {
            $form->setDefault('parent', $this->_parent->getId());
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oQuestion->setQuestion($form->getValue('question'));

                if ($form->getValue('parent') !== 'null') {
                    $oQuestion->setParent(SM_Module_GuestBook::getInstanceById($form->getValue('parent')));
                } else {
                    $oQuestion->setParent(null);
                }

                $oQuestion->setAnswer('');
                $oQuestion->setSubject('');
                $oQuestion->setName('');
                $oQuestion->setEmail('');
                $oQuestion->setIsModerate(true);
                $oQuestion->setIsFolder(true);

                try {
                    $oQuestion->insertToDb();
                    if ($this->_parent != null) {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
                    } else {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }

        }

        $this->view->form = $form;

        //$this->view->assign('question', $oQuestion);
        //$this->view->assign('folderList', SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));
    }

    public function editFolderAction()
    {
        $oQuestion = SM_Module_GuestBook::getInstanceById($this->getRequest()->getParam('id'));

        //Инициализируем форму
        $form = new Application_Form_GuestBook_Folder();
        $helperUrl = new Zend_View_Helper_Url();
        $form->setAction($helperUrl->url(array('controller' => 'guest-book', 'action' => 'edit-folder', 'id' => $oQuestion->getId())));
        if ($this->_parent != null) {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
        } else {
            $form->getElement('cancel')->setHref('/guest-book/index/link/' . $this->_link->getLink());
        }
        $form->submit->setLabel('Сохранить');

        $form->setParentList(SM_Module_GuestBook::getFolderList($this->_link, SM_Module_GuestBook::IS_ROOT));

        if ($oQuestion->getParent() != null) {
            $form->setDefault('parent', $oQuestion->getParent()->getId());
        }

        $form->setDefault('question', $oQuestion->getQuestion());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $oQuestion->setQuestion($form->getValue('question'));

                if ($form->getValue('parent') !== 'null') {
                    $oQuestion->setParent(SM_Module_GuestBook::getInstanceById($form->getValue('parent')));
                } else {
                    $oQuestion->setParent(null);
                }

                $oQuestion->setAnswer('');
                $oQuestion->setSubject('');
                $oQuestion->setName('');
                $oQuestion->setEmail('');

                $oQuestion->setIsModerate(true);
                $oQuestion->setIsFolder(true);

                try {
                    $oQuestion->updateToDB();
                    if ($this->_parent != null) {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
                    } else {
                        $this->_redirect('/guest-book/index/link/' . $this->_link->getLink());
                    }
                } catch (Exception $e) {
                    $this->view->assign('exception_msg', $e->getMessage());
                }
            }
        }

        $this->view->form = $form;

        //$this->view->assign('question', $oQuestion);
        //$this->view->assign('folderList', SM_Module_GuestBook::getFolderList($this->_link, $this->_parent));
    }

    public function deleteFolderAction()
    {
        $oQuestion = SM_Module_GuestBook::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oQuestion->deleteFromDB();
            if ($this->_parent != null) {
                $this->_redirect('/guest-book/index/link/' . $this->_link->getLink() . '/parent/' . $this->_parent->getId() . '/');
            } else {
                $this->_redirect('/guest-book/index/link/' . $this->_link->getLink());
            }
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }
}