<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 28.12.11
 * Time: 11:42
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE  TABLE IF NOT EXISTS `ekonom_pro_db`.`banner` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
   `img` VARCHAR(255) NOT NULL ,
   `link` VARCHAR(255) NOT NULL ,
   `title` VARCHAR(255) NOT NULL ,
   PRIMARY KEY (`id`) )
 ENGINE = InnoDB
 */

class SM_Banner_Banner
{
    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

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
     * @var string
     * @access protected
     */
    protected $_link;


    /**
     * @var TM_FileManager_Image|null
     */
    protected $_img = null;

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
    public function getLink()
    {
        return $this->_link;
    } // end of member function getDateCreate

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
     * @param string $value
     * @return string
     * @access public
     */
    public function setLink($value)
    {
        $this->_link = $value;
    }

    public function setImg($file)
    {
        $this->_img = $file;
    }

    public function getImg()
    {
        return $this->_img;
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
     * @return SM_Banner_Banner
     * @access public
     */
    public function __construct()
    {
        $config = Zend_Registry::get('production');
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

        $this->_img = new TM_FileManager_Image($config->banner->path);
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
            $sql = 'INSERT INTO banner(title, link) VALUES (:title, :link)';
            $this->_db->query($sql, array('title' => $this->_title, 'link' => $this->_link));

            $this->_id = $this->_db->lastInsertId();

            $fileName = $this->_img->download('img');
            if ($fileName !== false) {
                $this->_img->createPreview(190, 110);
                $this->_db->query('UPDATE banner SET img=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    //id, img, title, link

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
            $sql = 'UPDATE banner SET title=:title, link=:link WHERE id=:id';
            $this->_db->query($sql, array('title' => $this->_title, 'link' => $this->_link, 'id' => $this->_id));

            $fileName = $this->_img->download('img');
            if ($fileName !== false) {
                $this->_img->createPreview(190, 110);
                $this->_db->query('UPDATE banner SET img=:file WHERE id=:id', array('file' => $fileName, 'id' => $this->_id));
            }

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
    public function deleteFromDb()
    {
        try {
            $this->_img->delete();

            $sql = 'DELETE FROM banner WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function deleteFromDb

    /**
     *
     *
     * @param int $id идентификатор задачи
     * @return SM_Banner_Banner
     * @static
     * @access public
     */
    public static function getInstanceById($id)
    {
        try {
            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $sql = 'SELECT * FROM banner WHERE id=:id';
            $result = $db->query($sql, array('id' => (int)$id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Banner_Banner();
                $o->fillFromArray($result[0]);
                return $o;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function getInstanceById

    /**
     *
     *
     * @param array $values
     * @throws Exception
     * @return SM_Banner_Banner
     * @static
     * @access public
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Banner_Banner();
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

            $sql = 'SELECT * FROM banner';
            $result = $db->query($sql)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Banner_Banner::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } // end of member function getAllInstance

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
        $this->setLink($values['link']);
        $this->_img->setName($values['img']);
    }

}

?>
