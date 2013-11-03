<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 19.11.11
 * Time: 14:38
 * To change this template use File | Settings | File Templates.
 */

class TM_User_Hash
{

    /**
     *
     * @access protected
     */
    protected $_user;

    /**
     *
     * @access protected
     */
    protected $_attributeKey = '';

    /**
     *
     * @access protected
     */
    protected $_title;

    /**
     *
     * @access protected
     */
    protected $_type = null;

    /**
     * @var string
     */
    protected $_listValue = '';

    /**
     * @var Zend_Db_Adapter_Abstract
     * @access protected
     */
    protected $_db;

    /**
     *
     *
     * @return TM_User_User
     * @access public
     */
    public function getUser()
    {
        return $this->_user;
    } // end of member function getUser

    /**
     *
     *
     * @return TM_Attribute_AttribyteType
     * @access public
     */
    public function getType()
    {
        return $this->_type;
    } // end of member function getType

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getAttributeKey()
    {
        return $this->_attributeKey;
    } // end of member function getAttribyteKey

    public function setAttributeKey($value)
    {
        $this->_attributeKey = $value;
    }

    /**
     *
     *
     * @param TM_User_User $value
     *
     * @internal param $User ::TM_User_User value
     * @return void
    @access protected
     */
    protected function setUser(TM_User_User $value)
    {
        $this->_user = $value;

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
     * @param TM_Attribute_AttributeType $value
     *
     * @return void
     * @access protected
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
    public function setTitle($value)
    {
        $this->_title = $value;
    }

    /**
     *
     *
     * @param array|string $value
     *
     * @return void
     * @access public
     */
    public function setValueList($value)
    {
        if (is_array($value)) {
            $this->_listValue = implode('||', $value);
        } else {
            $this->_listValue = $value;
        }
    } // end of member function setValueList

    /**
     *
     *
     * @param bool $asString
     *
     * @return array|string
     * @access public
     */
    public function getValueList($asString = false)
    {
        if ($asString) {
            return $this->_listValue;
        } else {
            return explode('||', $this->_listValue);
        }

    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            throw new Exception('Can not find method ' . $method . ' in class ' . __CLASS__);
        }
    }

    /**
     *
     *
     * @return TM_User_Hash
     * @access public
     */
    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
    }

    /**
     *
     *
     * @return void
     * @access public
     */
    public function insertToDb()
    {
        try {
            $sql
                = 'INSERT INTO tm_user_hash(user_id, attribute_key, title, type_id, list_value)
                    VALUES (:user_id, :attribute_key, :title, :type_id, :list_value)';
            $this->_db->query($sql, array('user_id' => null, 'attribute_key' => $this->_attributeKey, 'title' => $this->_title, 'type_id' => $this->_type->getId(), 'list_value' => $this->_listValue));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @return void
     * @access public
     */
    public function updateToDb()
    {
        try {
            $sql
                = 'UPDATE tm_user_hash SET title=:title, type_id=:type_id, list_value=:list_value
                    WHERE user_id IS NULL AND attribute_key=:attribute_key';
            $this->_db->query($sql, array('attribute_key' => $this->_attributeKey, 'title' => $this->_title, 'type_id' => $this->_type->getId(), 'list_value' => $this->_listValue));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function updateToDb

    /**
     *
     *
     * @return void
     * @access public
     */
    public function deleteFromDb()
    {
        try {
            $sql = 'DELETE FROM tm_user_hash WHERE user_id IS NULL AND attribute_key=:attribute_key';
            $this->_db->query($sql, array('attribute_key' => $this->_attributeKey));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function deleteFromDb

    /**
     *
     *
     * @param int $key идентификатор задачи
     *
     * @return TM_User_Hash
     * @static
     * @access public
     */
    public static function getInstanceById($key)
    {
        try {
            $db = Zend_Registry::get('db');
            $sql = 'SELECT * FROM tm_user_hash WHERE user_id IS NULL AND attribute_key=:attribute_key';
            $result = $db->query($sql, array('attribute_key' => $key))->fetchAll();

            if (isset($result[0])) {
                $o = new TM_User_Hash();
                $o->fillFromArray($result[0]);
                return $o;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @param array $values
     *
     * @throws Exception
     * @return TM_User_Hash
     * @static
     * @access public
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new TM_User_Hash();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     * @param $object
     *
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance($object = null)
    {
        try {
            $db = Zend_Registry::get('db');
            $bind = array();

            $sql
                = 'SELECT tm_user_hash.attribute_key, title, tm_user_hash.type_id, list_value
                    FROM tm_user_hash ';
            if (!is_null($object)) {
                $sql
                    .= 'LEFT JOIN (
                        SELECT * FROM tm_user_attribute WHERE tm_user_attribute.user_id=:user_id
                     ) t2 ON tm_user_hash.attribute_key=t2.attribute_key
                     ORDER BY t2.is_fill DESC, title';
                $bind['user_id'] = $object->id;
            } else {
                $sql .= ' ORDER BY title';
            }

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = TM_User_Hash::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
        $this->setAttributeKey($values['attribute_key']);
        $this->setTitle($values['title']);

        $this->setType(TM_Attribute_AttributeTypeFactory::getAttributeTypeById(new TM_User_AttributeTypeMapper(), $values['type_id']));
        $this->setValueList($values['list_value']);
    }

}
