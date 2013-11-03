<?php


/**
 * class TM_Attribute_AttributeType
 *
 */
class TM_Attribute_AttributeType
{
    /**
     *
     * @access protected
     */
    protected $_id;

    /**
     *
     * @access protected
     */
    protected $_title;

    /**
     *
     * @access protected
     */
    protected $_handler;

    /**
     * @var string
     * @access protected
     */
    protected $_description;


    protected $_db;

    /**
     *
     *
     * @return int
     * @access public
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     *
     *
     * @param int $value
     *
     * @return void
     * @access protected
     */
    protected function setId($value)
    {
        $this->_id = (int)$value;
    }

    /**
     *
     *
     * @param string $value
     *
     * @return void
     * @access public
     */
    public function setTitle($value)
    {
        $this->_title = $value;
    }

    /**
     *
     *
     * @param string $value
     *
     * @return void
     * @access public
     */
    public function setHandler($value)
    {
        $this->_handler = $value;
    }

    /**
     *
     *
     * @param string $value
     *
     * @return void
     * @access public
     */
    public function setDescription($value)
    {
        $this->_description = $value;
    }

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
     *
     * @return TM_Attribute_AttributeType
     * @access   public
     */
    public function __construct()
    {
    }

    /**
     * @param $hash
     * @param $object
     * @param $value
     *
     * @return void
     */
    public function getHTMLFrom($hash, $object, $value = '')
    {
        $html = '<input type="text" name="data[attribute][' . $hash->attributeKey . ']" value="';
        if ($object->searchAttribute($hash->attributeKey)) {
            $html .= $object->getAttribute($hash->attributeKey)->value;
        }
        $html .= '"/>';
        echo $html;
    }

    public function getHTML($hash, $object)
    {
        $html = '';
        if ($object->searchAttribute($hash->attributeKey)) {
            $html .= $object->getAttribute($hash->attributeKey)->value;
        }
        echo $html;
    }

    /**
     *
     *
     * @param array $values
     *
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setDescription($values['description']);
        $this->setHandler($values['handler']);
    }

}