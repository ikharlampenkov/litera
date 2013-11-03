<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 20.11.11
 * Time: 20:16
 * To change this template use File | Settings | File Templates.
 */

abstract class TM_Attribute_AttributeTypeMapper
{

    /**
     * @var TM_Attribute_AttributeType
     */
    protected $_type = null;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @return TM_Attribute_AttributeTypeMapper
     * @access public
     */
    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
    }

    /**
     *
     *
     * @param TM_Attribute_AttributeType $type
     *
     * @return void void
     * @abstract
     * @access public
     */
    abstract public function insertToDB(TM_Attribute_AttributeType $type);

    /**
     *
     *
     * @param TM_Attribute_AttributeType $type
     *
     * @return void
     * @abstract
     * @access public
     */
    abstract public function updateToDB(TM_Attribute_AttributeType $type);

    /**
     *
     *
     * @param TM_Attribute_AttributeType $type
     *
     * @return void
     * @abstract
     * @access public
     */
    abstract public function deleteFromDB(TM_Attribute_AttributeType $type);

    /**
     *
     *
     * @param int $id
     *
     * @return TM_Attribute_AttributeType
     * @abstract
     * @access public
     */
    abstract public function getInstanceById($id);

    /**
     *
     *
     * @param array $values
     *
     * @return TM_Attribute_Attribute
     * @access public
     */
    abstract public function getInstanceByArray($values);

    /**
     *
     *
     * @return void array
     * @abstract
     * @access public
     */
    abstract public function getAllInstance();

    /**
     * @abstract
     *
     * @param int $id
     *
     * @return void
     */
    abstract public function selectFromDB($id);

}
