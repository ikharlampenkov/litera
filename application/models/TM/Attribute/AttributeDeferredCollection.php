<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 10.10.13
 * Time: 22:42
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class TM_Attribute_AttributeCollection
 */
class TM_Attribute_AttributeDeferredCollection extends TM_Attribute_AttributeCollection
{
    private $_sql = '';

    private $_run = false;

    private $_objectId = -1;

    /**
     * @param TM_User_User  $object
     * @param array $sql
     * @param null  $mapper
     */
    public function __construct($object = null, $sql, $mapper = null)
    {
        parent::__construct($object, null, $mapper);
        $this->_sql = $sql;
        $this->_objectId = $object->getId();
    }

    public function targetClass()
    {
        return 'TM_Attribute_Attribute';
    }


    protected function notifyAccess()
    {
        if (!$this->_run) {
            $db = Zend_Registry::get('db');
            $this->raw = $db->query($this->_sql, array('user_id' => $this->_objectId))->fetchAll();
            $this->total = count($this->raw);
        }
        $this->_run = true;
    }
}