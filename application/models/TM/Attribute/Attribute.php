<?php

require_once 'AttributeType.php';
require_once 'Attribute.php';


/**
 * class TM_Attribute_Attribute
 *
 */
class TM_Attribute_Attribute
{
    /**
     * @var TM_User_User
     * @access protected
     */
    protected $_object = null;

    /**
     *
     * @access protected
     */
    protected $_attributeKey;

    /**
     *
     * @access protected
     */
    protected $_type = null;

    /**
     *
     * @access protected
     */
    protected $_value;

    /**
     *
     *
     * @return TM_Task_Task
     * @access public
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     *
     *
     * @return TM_Attribute_AttributeType
     * @access public
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getAttributeKey()
    {
        return $this->_attributeKey;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     *
     *
     * @param TM_User_User $value
     *
     * @return void
     * @access protected
     */
    protected function setObject(TM_User_User $value)
    {
        $this->_object = $value;

    }

    /**
     *
     *
     * @param string $value
     *
     * @return void
     * @access public
     */
    public function setAttributeKey($value)
    {
        $this->_attributeKey = $value;
    }

    /**
     *
     *
     * @param TM_Attribute_AttributeType $value
     *
     * @return void
     * @access public
     */
    public function setType(TM_Attribute_AttributeType $value)
    {
        $this->_type = $value;
    }

    /**
     *
     *
     * @param string $value
     *
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        if ($value === 'on') {
            $this->_value = 1;
        }
        $this->_value = trim($value);
    }

    /**
     *
     * @param string $name
     *
     * @throws Exception
     * @return mixed
     */

    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            throw new Exception('Can not find method ' . $method . ' in class ' . __CLASS__);
        }
    }

    /**
     *
     * @param                              $object
     *
     * @return TM_Attribute_Attribute
     * @access   public
     */
    public function __construct($object)
    {
        $this->_object = $object;
    }

    /**
     *
     *
     * @param array $values
     *
     * @return void
     */
    public function fillFromArray($values)
    {
        $oMapper = new TM_User_AttributeTypeMapper();
        $o_type = $oMapper->getInstanceById($values['type_id']);
        $this->setType($o_type);

        $this->setAttributeKey($values['attribute_key']);
        $this->setValue($values['attribute_value']);
    }

}