<?php

class UserController extends Zend_Controller_Action
{


    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->assign('userRoleList', TM_User_Role::getAllInstance());
        $this->view->assign('userList', TM_User_User::getAllInstance());
    }

    public function viewattributetypeAction()
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $this->view->assign('attributeTypeList', $oMapper->getAllInstance());
    }

    public function viewhashAction()
    {
        $this->view->assign('attributeHashList', TM_User_Hash::getAllInstance());
    }

    public function viewresourceAction()
    {
        $this->view->assign('userResourceList', TM_User_Resource::getAllInstance());
    }

    public function addAction()
    {
        $oUser = new TM_User_User();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oUser = new TM_User_User();
            $oUser->setLogin($data['login']);
            $oUser->setDateCreate($data['date_create']);
            $oUser->setPassword($data['password']);
            $oUser->setRole(TM_User_Role::getInstanceById($data['role_id']));

            try {
                $oUser->insertToDb();
                $this->redirect('/user');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('user', $oUser);
        $this->view->assign('userRoleList', TM_User_Role::getAllInstance());
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $oUser = TM_User_User::getInstanceById($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');


            $oUser->setLogin($data['login']);
            $oUser->setDateCreate($data['date_create']);
            $oUser->setPassword($data['password']);
            $oUser->setRole(TM_User_Role::getInstanceById($data['role_id']));

            foreach ($data['attribute'] as $key => $value) {
                $oUser->setAttribute($key, $value);
            }

            try {
                $oUser->updateToDb();
                $this->redirect('/user');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('userRoleList', TM_User_Role::getAllInstance());
        $this->view->assign('attributeHashList', TM_User_Hash::getAllInstance($oUser));
        $this->view->assign('user', $oUser);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        $oUser = TM_User_User::getInstanceById($id);
        $oUser->deleteFromDB();

        $this->redirect('/user');
    }

    public function addroleAction()
    {
        $oRole = new TM_User_Role();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oRole = new TM_User_Role();
            $oRole->setTitle($data['title']);
            $oRole->setRtitle($data['rtitle']);

            try {
                $oRole->insertToDb();
                $this->redirect('/user');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('role', $oRole);
    }

    public function editroleAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oRole = TM_User_Role::getInstanceById($id);
            $oRole->setTitle($data['title']);
            $oRole->setRtitle($data['rtitle']);

            try {
                $oRole->updateToDb();
                $this->redirect('/user');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('role', TM_User_Role::getInstanceById($id));
    }

    public function deleteroleAction()
    {
        $id = $this->getRequest()->getParam('id');

        $oRole = TM_User_Role::getInstanceById($id);
        $oRole->deleteFromDB();

        $this->redirect('/user');
    }

    public function showroleaclAction()
    {
        $oRole = TM_User_Role::getInstanceById($this->getRequest()->getParam('idRole'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            try {
                foreach ($data as $idResource => $values) {
                    $roleAcl = new TM_User_RoleAcl($oRole);
                    $roleAcl->setResource(TM_User_Resource::getInstanceById($idResource));
                    $roleAcl->setIsAllow($values['is_allow']);
                    $roleAcl->setPrivileges($values['privileges']);
                    $roleAcl->saveToDb();
                }

                $this->redirect('/user/showRoleAcl/idRole/' . $this->getRequest()->getParam('idRole'));
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('role', $oRole);
        $this->view->assign('userResourceList', TM_User_Resource::getAllInstance());
        $this->view->assign('roleAcl', TM_User_RoleAcl::getAllInstance($oRole));
    }

    public function addresourceAction()
    {
        $oResource = new TM_User_Resource();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oResource->setTitle($data['title']);
            $oResource->setRtitle($data['rtitle']);

            try {
                $oResource->insertToDb();
                $this->redirect('/user/viewResource');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('resource', $oResource);
    }

    public function editresourceAction()
    {
        $oResource = TM_User_Resource::getInstanceById($this->getRequest()->getParam('id'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $oResource->setTitle($data['title']);
            $oResource->setRtitle($data['rtitle']);

            try {
                $oResource->updateToDb();
                $this->redirect('/user/viewResource');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }
        }

        $this->view->assign('resource', $oResource);
    }

    public function deleteresourceAction()
    {
        $oResource = TM_User_Resource::getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oResource->deleteFromDB();
            $this->_redirect('/user/viewResource');
        } catch (Exception $e) {
            $this->view->assign('exception_msg', $e->getMessage());
        }
    }

    public function addattributetypeAction()
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $oType = new TM_Attribute_AttributeType();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oType->setTitle($data['title']);
            $oType->setDescription($data['description']);
            $oType->setHandler($data['handler']);

            try {
                $oMapper->insertToDb($oType);
                $this->redirect('/user/viewAttributeType');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('type', $oType);
    }

    public function editattributetypeAction()
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $oType = $oMapper->getInstanceById($this->getRequest()->getParam('id'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oType->setTitle($data['title']);
            $oType->setDescription($data['description']);
            $oType->setHandler($data['handler']);

            try {
                $oMapper->updateToDb($oType);
                $this->redirect('/user/viewAttributeType');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('type', $oType);
    }

    public function deleteattributetypeAction()
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $oType = $oMapper->getInstanceById($this->getRequest()->getParam('id'));
        try {
            $oType->deleteFromDB();
            $this->redirect('/user/viewAttributeType');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function addattributehashAction()
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $oHash = new TM_User_Hash();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oHash->setAttributeKey($data['attribute_key']);
            $oHash->setTitle($data['title']);
            $oHash->setType($oMapper->getInstanceById($data['type_id']));
            $oHash->setValueList($data['list_value']);

            try {
                $oHash->insertToDb();
                $this->redirect('/user/viewHash');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('hash', $oHash);
        $this->view->assign('attributeTypeList', $oMapper->getAllInstance());
    }

    public function editattributehashAction()
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $oHash = TM_User_Hash::getInstanceById($this->getRequest()->getParam('key'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oHash->setTitle($data['title']);
            $oHash->setType($oMapper->getInstanceById($data['type_id']));
            $oHash->setValueList($data['list_value']);

            try {
                $oHash->updateToDb();
                $this->redirect('/user/viewHash');
            } catch (Exception $e) {
                $this->view->assign('exception_msg', $e->getMessage());
            }

        }

        $this->view->assign('hash', $oHash);
        $this->view->assign('attributeTypeList', $oMapper->getAllInstance());
    }

    public function deleteattributehashAction()
    {
        $oHash = TM_User_Hash::getInstanceById($this->getRequest()->getParam('key'));
        try {
            $oHash->deleteFromDB();
            $this->redirect('/user/viewHash');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function fillresourceAction()
    {
        $acl = new TM_Acl_Acl();
        foreach ($acl->getResources() as $resource) {
            print_r($resource);
            $res = new TM_User_Resource();
            $res->setTitle($resource);
            try {
                $res->insertToDB();

            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function registrationAction()
    {
        $oUser = new TM_User_User();
        $oUser->setRole(TM_User_Role::getInstanceById(7));
        $oUser->setDateCreate(date('d.m.Y H:i:s'));

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');

            $oUser->setLogin($data['login']);
            $oUser->setPassword($data['password']);

            foreach ($data['attribute'] as $key => $value) {
                $oUser->setAttribute($key, $value);
            }

            $oUser->setAttribute('email', $data['login']);

            try {
                if (isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] === $data['captcha']) {
                    $oUser->insertToDb();
                    $this->_redirect('/');
                } else {
                    throw new Exception('Введите код с картинки заново');
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        $this->view->assign('user', $oUser);
        $this->view->assign('attributeHashList', TM_User_Hash::getAllInstance($oUser));
    }

}