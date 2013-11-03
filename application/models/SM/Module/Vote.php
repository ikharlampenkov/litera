<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 27.05.12
 * Time: 21:38
 * To change this template use File | Settings | File Templates.
 */

/*
 * CREATE TABLE IF NOT EXISTS `vote_email` (
   `link_id` int(10) unsigned NOT NULL,
   `val` varchar(255) NOT NULL,
   PRIMARY KEY (`link_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

class SM_Module_Vote
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    public function  __construct()
    {
        $config = Zend_Registry::get('production');
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
    }

    /**
     * @static
     * @param $link
     * @return array|bool
     * @throws Exception
     */
    public static function getVoteEmail($link)
    {
        try {
            $sql = 'SELECT * FROM vote_email WHERE link_id=:link_id';
            $config = Zend_Registry::get('production');

            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
            $result = $db->query($sql, array('link_id' => $link->getId()))->fetchAll();

            if (isset($result[0])) {
                return array('link_id' => $result[0]['link_id'], 'email' => $result[0]['val']);
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function saveVoteEmail($link, $email)
    {
        try {
            $sql = 'REPLACE INTO vote_email(link_id, val) VALUES (:link_id, :val)';
            $config = Zend_Registry::get('production');

            $db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);
            $db->query($sql, array('link_id' => $link->getId(), 'val' => $email));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function sendVote($link, $data)
    {
        $mail = static::getVoteEmail($link);

        $message = 'Вопрос из Интернет-приемной: ' . "\r\n";
        $message .= 'Дата: ' . date('d.m.Y') . "\r\n" .
                'Фамилия имя отчество: ' . $data['fio'] . "\r\n" .
                'Почтовый адрес: ' . $data['address'] . "\r\n" .
                'E-mail: ' . $data['email'] . "\r\n" .
                'Текст сообщения: ' . $data['short_text'] . "\r\n" . "\r\n"; //'Телефон: ' . $data['phone'] . "\r\n" .

        $header = 'From: vote@мохово.рф';
        if (!empty($mail['email'])) {
            mail($mail['email'], 'Интернет-приемная', mb_convert_encoding($message, 'windows-1251', 'UTF-8'), $header);
        }


    }

}
