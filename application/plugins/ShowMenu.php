<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 16.12.11
 * Time: 22:07
 * To change this template use File | Settings | File Templates.
 */
class ShowMenu extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('View');
        $topMenu = SM_Menu_Menu::getInstanceByCode('verhnee_menu');
        $view->assign('topMenuList', $topMenu->getItemList());


        $oItem = SM_Menu_Item::getInstanceByLink($request->getParam('link'));
        if ($oItem) {
            $oParent = $oItem->getParent();
        } else {
            $oParent = null;
        }

        $view->assign('menuActiveParent', $oParent);
        $view->assign('menuActiveItem', $oItem);

    }

}
