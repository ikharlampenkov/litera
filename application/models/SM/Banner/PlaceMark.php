<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 28.12.11
 * Time: 13:04
 * To change this template use File | Settings | File Templates.
 */
class SM_Banner_PlaceMark
{

    /**
     * @var SM_Banner_Place
     */
    protected $_place;

    /**
     * @var SM_Banner_Banner
     */
    protected $_banner;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @param SM_Banner_Banner $banner
     */
    public function setBanner($banner)
    {
        $this->_banner = $banner;
    }

    /**
     * @return SM_Banner_Banner
     */
    public function getBanner()
    {
        return $this->_banner;
    }

    /**
     *
     *
     * @param SM_Banner_Place $place
     */
    public function setPlace($place)
    {
        $this->_place = $place;
    }

    /**
     * @return SM_Banner_Place
     */
    public function getPlace()
    {
        return $this->_place;
    }

    /**
     * @param $place
     * @return SM_Banner_PlaceMark
     */
    public function __construct($place)
    {
        $config = Zend_Registry::get('production');
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

        $this->_place = $place;
    }

    /**
     * @throws Exception
     */
    public function saveToDb()
    {
        try {
            $sql = 'REPLACE INTO banner_place(banner_id, bplace_id)
                                VALUES (:banner_id, :bplace_id)';
            $this->_db->query($sql, array('banner_id' => $this->_banner->getId(), 'bplace_id' => $this->_place->getId()));

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function deleteFromDB($place)
    {
        try {
            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $sql = 'DELETE FROM banner_place WHERE bplace_id=:bplace_id';
            $db->query($sql, array('bplace_id' => $place->getId()));

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    //banner_id, bplace_id

    /**
     *
     * @param $place
     * @param array $values
     * @return SM_Banner_PlaceMark
     * @static
     * @access public
     */
    public static function getInstanceByArray($place, $values)
    {
        try {
            $o = new SM_Banner_PlaceMark($place);
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     * @param $place
     * @param $city_id
     * @return array
     * @static
     * @access public
     */
    public static function getAllInstance($place)
    {
        try {
            $config = Zend_Registry::get('production');
            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

            $sql = 'SELECT * FROM banner_place, banner
                    WHERE banner_place.banner_id=banner.id AND bplace_id=:bplace_id';
            $result = $db->query($sql, array('bplace_id' => $place->getId()))->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[$res['banner_id']] = SM_Banner_PlaceMark::getInstanceByArray($place, $res);
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
        $this->setBanner(SM_Banner_Banner::getInstanceById($values['banner_id']));
    }
}
