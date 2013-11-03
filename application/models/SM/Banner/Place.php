<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 28.12.11
 * Time: 11:44
 * To change this template use File | Settings | File Templates.
 */
/*
 * CREATE  TABLE IF NOT EXISTS `ekonom_pro_db`.`bplace` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
   `title` VARCHAR(255) NOT NULL ,
   `width` INT UNSIGNED NOT NULL ,
   `height` INT UNSIGNED NOT NULL ,
   PRIMARY KEY (`id`) )
 ENGINE = InnoDB


CREATE  TABLE IF NOT EXISTS `ekonom_pro_db`.`banner_place` (
  `banner_id` INT UNSIGNED NOT NULL ,
  `bplace_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`banner_id`, `bplace_id`) ,
  INDEX `fk_banner_place_banner1` (`banner_id` ASC) ,
  CONSTRAINT `fk_banner_place_bplace1`
    FOREIGN KEY (`bplace_id` )
    REFERENCES `ekonom_pro_db`.`bplace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_banner_place_banner1`
    FOREIGN KEY (`banner_id` )
    REFERENCES `ekonom_pro_db`.`banner` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
 */

class SM_Banner_Place
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
     * @var int
     * @access protected
     */
    protected $_width;

    /**
     * @var int
     * @access protected
     */
    protected $_height;

    /**
     * @var int - время между сменой баннеров
     */
    protected $_changeTime = 5;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;


    /**
     *
     *
     * @return int
     * @access public
     */
    public function getId()
    {
        return $this->_id;
    } // end of member function getId

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return $this->_title;
    } // end of member function getTitle


    /**
     *
     *
     * @return int
     * @access public
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     *
     *
     * @return int
     * @access public
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     *
     *
     * @param int $value
     * @return void
     * @access protected
     */
    protected function setId($value)
    {
        $this->_id = (int)$value;
    } // end of member function setId

    /**
     *
     *
     * @param string $value
     * @return void
     * @access public
     */
    public function setTitle($value)
    {
        $this->_title = $value;
    } // end of member function setTitle

    /**
     *
     *
     * @param int $value
     * @return void
     * @access public
     */
    public function setWidth($value)
    {
        $this->_width = $value;
    }

    /**
     *
     *
     * @param int $value
     * @return void
     * @access public
     */
    public function setHeight($value)
    {
        $this->_height = $value;
    }

    /**
     * @param int $changeTime
     */
    public function setChangeTime($changeTime)
    {
        $this->_changeTime = (int)$changeTime;
    }

    /**
     * @return int
     */
    public function getChangeTime()
    {
        return $this->_changeTime;
    }

    public function __get($name)
    {
        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    /**
     *
     *
     * @return SM_Banner_Place
     * @access public
     */
    public function __construct()
    {
        $config = Zend_Registry::get('production');
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
    }

    /**
     *
     *
     * @throws Exception
     * @return void
     * @access public
     */
    public function insertToDb()
    {
        try {
            $sql = 'INSERT INTO bplace(title, width, height, change_time) VALUES (:title, :width, :height, :change_time)';
            $this->_db->query($sql, array('title' => $this->_title, 'width' => $this->_width, 'height' => $this->_height, 'change_time' => $this->_changeTime));

            $this->_id = $this->_db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //id, title, width, height

    /**
     *
     *
     * @throws Exception
     * @return void
     * @access public
     */
    public function updateToDb()
    {
        try {
            $sql = 'UPDATE bplace
                       SET title=:title, width=:width, height=:height, change_time=:change_time
                     WHERE id=:id';
            $this->_db->query($sql, array('title' => $this->_title, 'width' => $this->_width, 'height' => $this->_height, 'change_time' => $this->_changeTime, 'id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @throws Exception
     * @return void
     * @access public
     */
    public function deleteFromDb()
    {
        try {
            $sql = 'DELETE FROM bplace WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     *
     * @param int $id идентификатор площадки для баннера
     * @throws Exception
     * @return SM_Banner_Place
     * @static
     * @access public
     */
    public static function getInstanceById($id)
    {
        try {
            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $sql = 'SELECT * FROM bplace WHERE id=:id';
            $result = $db->query($sql, array('id' => (int)$id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Banner_Place();
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
     * @throws Exception
     * @return SM_Banner_Place
     * @static
     * @access public
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Banner_Place();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function getInstanceByArray

    /**
     *
     *
     * @throws Exception
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance()
    {
        try {
            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $sql = 'SELECT * FROM bplace';
            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Banner_Place::getInstanceByArray($res);
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
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setTitle($values['title']);
        $this->setWidth($values['width']);
        $this->setHeight($values['height']);
        $this->setChangeTime($values['change_time']);
    }
}