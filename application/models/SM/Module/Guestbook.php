<?php
/*
CREATE TABLE guestbook
(
  id bigserial NOT NULL,
  link_id bigint NOT NULL,
  name character varying(50) NOT NULL,
  email character varying(50) NOT NULL,
  subject character varying(255) NOT NULL,
  question text,
  answer text,
  moderate boolean NOT NULL DEFAULT false,
  date_create date NOT NULL,
  parent_id bigint,
  is_folder boolean NOT NULL DEFAULT false,
  CONSTRAINT guestbook_pkey PRIMARY KEY (id),
  CONSTRAINT guestbook_link_id_fkey FOREIGN KEY (link_id)
      REFERENCES menu_item (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT guestbook_parent_id_fkey FOREIGN KEY (parent_id)
      REFERENCES guestbook (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE guestbook
  OWNER TO garage;
COMMENT ON TABLE guestbook
  IS 'Вопрос-Ответ';
*/

/**
 * Class SM_Module_GuestBook
 */
class SM_Module_GuestBook
{

    const IS_MODERATE = 1;

    const IS_NO_MODERATE = 0;

    const IS_ANY_MODERATE = null;

    const IS_FOLDER = 1;

    const IS_ROOT = null;

    /**
     * @var int
     */
    private $_id = 0;

    /**
     * @var SM_Menu_Item
     */
    private $_link = null;

    /**
     * @var string
     */
    private $_name = '';

    /**
     * @var string
     */
    private $_email = '';

    /**
     * @var string
     */
    private $_subject = '';

    /**
     * @var string
     */
    private $_question = '';

    /**
     * @var SM_Module_GuestBook|null
     */
    private $_parent = null;

    /**
     * @var bool
     */
    private $_isFolder = false;

    /**
     * @var string
     */
    private $_answer = '';

    /**
     * @var bool
     */
    private $_moderate = false;

    /**
     * @var string
     */
    private $_dateCreate = '';

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db = null;

    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->_answer = $answer;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->_answer;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param \SM_Menu_Item $link
     */
    public function setLink($link)
    {
        $this->_link = $link;
    }

    /**
     * @return \SM_Menu_Item
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * @param boolean $moderate
     */
    public function setIsModerate($moderate)
    {
        $this->_moderate = $moderate;
    }

    /**
     * @return boolean
     */
    public function getIsModerate()
    {
        return $this->_moderate;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->_question = $question;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->_question;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @param string $dateCreate
     */
    public function setDateCreate($dateCreate)
    {
        $this->_dateCreate = date('Y-m-d', strtotime($dateCreate));
    }

    /**
     * @return string
     */
    public function getDateCreate()
    {
        return $this->_dateCreate;
    }

    /**
     * @param boolean $isFolder
     */
    public function setIsFolder($isFolder)
    {
        $this->_isFolder = $isFolder;
    }

    /**
     * @return boolean
     */
    public function getIsFolder()
    {
        return $this->_isFolder;
    }

    /**
     * @param null|\SM_Module_GuestBook $parent
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }

    /**
     * @return null|\SM_Module_GuestBook
     */
    public function getParent()
    {
        return $this->_parent;
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
     * @param null|SM_Module_GuestBook $value
     *
     * @return int|null
     */
    protected function _prepareNull($value)
    {
        if (is_null($value) || empty($value)) {
            return null;
        } else {
            return $value->getId();
        }
    }

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');

        $this->_dateCreate = date('Y-m-d');
    }

    public function insertToDB()
    {
        try {
            $sql
                = 'INSERT INTO guestbook(link_id, name, email, subject, question, answer, moderate, date_create, parent_id, is_folder)
                             VALUES(:link_id, :name, :email, :subject, :question, :answer, :moderate, :date_create, :parent_id, :is_folder)';
            $this->_db->query(
                $sql,
                array('link_id' => $this->_link->getId(), 'name' => $this->_name, 'date_create' => $this->_dateCreate,
                      'email'   => $this->_email, 'subject' => $this->_subject, 'question' => $this->_question,
                      'answer'  => $this->_answer, 'moderate' => (int)$this->_moderate, 'parent_id' => $this->_prepareNull($this->_parent), 'is_folder' => (int)$this->_isFolder)
            );

            if ($this->_moderate == false) {
                $this->_sendMail($this->_name, $this->_answer);
            }

            $this->_id = $this->_db->lastInsertId('guestbook', 'id');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //link_id, title, date_public, date_create, short_text, full_text, file

    public function updateToDB()
    {
        try {
            $sql
                = 'UPDATE guestbook
                      SET link_id=:link_id, name=:name, date_create=:date_create, subject=:subject,
                           email=:email, question=:question, answer=:answer, moderate=:moderate, parent_id=:parent_id, is_folder=:is_folder
                    WHERE id=:id';
            $this->_db->query(
                $sql, array('link_id' => $this->_link->getId(), 'name' => $this->_name, 'date_create' => $this->_dateCreate,
                            'email'   => $this->_email, 'subject' => $this->_subject, 'question' => $this->_question,
                            'answer'  => $this->_answer, 'moderate' => (int)$this->_moderate, 'parent_id' => $this->_prepareNull($this->_parent), 'is_folder' => (int)$this->_isFolder, 'id' => $this->_id)
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFromDB()
    {
        try {
            $sql = 'DELETE FROM guestbook WHERE id=:id';
            $this->_db->query($sql, array('id' => $this->_id));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param  SM_Menu_Item $link
     * @param null          $parent
     * @param null          $moderate
     *
     * @throws Exception
     * @return array|bool
     */
    public static function getAllInstance($link, $parent = null, $moderate = null)
    {
        try {
            $db = Zend_Registry::get('db');

            $sql = 'SELECT * FROM guestbook WHERE link_id=:link_id ';
            $bind = array('link_id' => $link->getId());

            if ($parent == null) {
                $sql .= ' AND parent_id IS NULL';
            } else {
                $sql .= ' AND parent_id=:parent';
                $bind['parent'] = $parent->getId();
            }

            if ($moderate != null) {
                $sql .= ' AND moderate=:moderate ';
                $bind['moderate'] = $moderate;
            }

            $sql .= ' ORDER BY is_folder DESC, moderate DESC, date_create DESC';

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_GuestBook::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param SM_Menu_Item $link
     * @param SM_Module_GuestBook|null $parent
     * @return array|bool
     * @throws Exception
     */
    public static function getFolderList(SM_Menu_Item $link, $parent = null)
    {
        try {
            $db = Zend_Registry::get('db');

            $sql = 'SELECT * FROM guestbook WHERE link_id=:link_id AND is_folder=:is_folder ';
            $bind = array('link_id' => $link->getId(), 'is_folder' => true);

            if ($parent == null) {
                $sql .= ' AND parent_id IS NULL';
            } else {
                $sql .= ' AND parent_id=:parent';
                $bind['parent'] = $parent->getId();
            }

            $sql .= ' ORDER BY id';

            $result = $db->query($sql, $bind)->fetchAll();

            if (isset($result[0])) {
                $retArray = array();
                foreach ($result as $res) {
                    $retArray[] = SM_Module_GuestBook::getInstanceByArray($res);
                }
                return $retArray;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param $values
     *
     * @return SM_Module_Document
     * @throws Exception
     */
    public static function getInstanceByArray($values)
    {
        try {
            $o = new SM_Module_GuestBook();
            $o->fillFromArray($values);
            return $o;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @static
     *
     * @param $id
     *
     * @return bool|SM_Module_GuestBook
     * @throws Exception
     */
    public static function getInstanceById($id)
    {
        try {
            $sql = 'SELECT * FROM guestbook WHERE id=:id';

            $db = Zend_Registry::get('db');
            $result = $db->query($sql, array('id' => $id))->fetchAll();

            if (isset($result[0])) {
                $o = new SM_Module_GuestBook();
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
     * @return void
     * @access public
     */
    public function fillFromArray($values)
    {
        $this->setId($values['id']);
        $this->setName($values['name']);
        $this->setDateCreate($values['date_create']);

        $this->setSubject($values['subject']);
        $this->setEmail($values['email']);
        $this->setQuestion($values['question']);
        $this->setAnswer($values['answer']);
        $this->setIsModerate($values['moderate']);
        $this->setIsFolder($values['is_folder']);

        if (!empty($values['parent_id'])) {
            $this->setParent(SM_Module_GuestBook::getInstanceById($values['parent_id']));
        }

        $oMenuItem = SM_Menu_Item::getInstanceById($values['link_id']);
        $this->setLink($oMenuItem);
    }

    /*
    public function getAnswerCount()
    {
        try {
            $result = $this->_db->query('SELECT COUNT(id) AS ans_count, catalogid FROM tblguestbook WHERE moderate=1 GROUP BY catalogid', 2);

            if (isset($result) && !empty($result)) {
                $ret_array = array();
                foreach ($result as $res) {
                    $ret_array[$res['catalogid']] = $res['ans_count'];
                }
                return $ret_array;
            } else return false;

        } catch (Exception $e) {
            echo '<!--' . $e->getMessage() . '-->';
        }
    }
    */

    private function _sendMail($name, $uMessage)
    {
        try {
            $mail = SM_Module_Vote::getVoteEmail($this->_link);

            if (!empty($mail['email'])) {
                $message
                    = 'Пожалуйста,  не отвечайте на это письмо.
                        Оно отослано Вам автоматической службой отправки писем.

                        Посетитель задал вопрос. Информация о посетителе: 
 
                        ФИО: ' . $name . '
                        Вопрос: ' . $uMessage . '
 
                        С наилучшими пожеланиями,
                        Автоматический Диспетчер Запросов';
                $subject = mb_convert_encoding('Автоматический Диспетчер Запросов', 'windows-1251', 'UTF-8');
                $message = mb_convert_encoding($message, 'windows-1251', 'UTF-8');

                $config = Zend_Registry::get('production');
                $header = 'From: ' . $config->guestbook->fromEmail;
                return mail($mail['email'], $subject, $message, $header);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }


    public function getChild()
    {
        return SM_Module_GuestBook::getFolderList($this->_link, $this);
    }

    /**
     * Позволяет оперделить наличие потомков
     *
     * @param int $isFolder
     *
     * @throws Exception
     * @return bool
     */
    public function hasChild($isFolder = SM_Module_GuestBook::IS_FOLDER)
    {
        try {
            $sql = 'SELECT COUNT(id) AS child_cnt FROM guestbook WHERE parent_id=:parent AND is_folder=:is_folder';
            $result = $this->_db->query($sql, array('parent' => $this->_id, 'is_folder' => $isFolder))->fetch();

            if (isset($result['child_cnt']) && $result['child_cnt'] > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}