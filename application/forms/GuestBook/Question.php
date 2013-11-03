<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 04.08.13
 * Time: 22:19
 * To change this template use File | Settings | File Templates.
 */

class Application_Form_GuestBook_Question extends Twitter_Bootstrap_Form_Horizontal
{
    public function setParentList($parentList)
    {
        if (!empty($parentList) && $parentList != false) {
            $element = $this->getElement('parent');

            foreach ($parentList as $folder) {
                $element->addMultiOption($folder->getId(), $folder->getQuestion());
                if ($folder->hasChild()) {
                    $this->_setChildList($folder->getChild(), $element, '--');
                }
            }
        }
    }

    protected function _setChildList($child, $element, $wid)
    {
        foreach ($child as $folder) {
            $element->addMultiOption($folder->getId(), $wid . $folder->getQuestion());
            if ($folder->hasChild()) {
                $this->_setChildList($folder->getChild(), $element, $wid . '--');
            }
        }
    }

    public function init()
    {
        // Указываем метод формы
        $this->setMethod('post');

        $this->setIsArray(true);
        $this->setElementsBelongTo('Folder');


        $this->addElement(
            'textarea', 'question',
            array(
                 'label'       => 'Вопрос',
                 'placeholder' => 'Вопрос',
                 'required'    => true,
                 'validators'  => array(
                     array('StringLength', true, array(0, 5000))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'select', 'parent',
            array('label'    => 'Родительский элемент',
                  'required' => true
            )
        );

        $this->addElement(
            'textarea', 'answer',
            array(
                 'label'       => 'Ответ',
                 'placeholder' => 'Ответ',
                 'required'    => false,
                 'validators'  => array(
                     array('StringLength', true, array(0, 5000))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'text', 'name',
            array(
                 'label'       => 'ФИО',
                 'placeholder' => 'Фамилия Имя Отчество',
                 'required'    => false,
                 'maxlength'   => '255',
                 'validators'  => array(
                     array('StringLength', true, array(0, 255))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'text', 'email',
            array(
                 'label'       => 'E-mail',
                 'placeholder' => 'E-mail',
                 'required'    => false,
                 'maxlength'   => '255',
                 'validators'  => array(
                     array('StringLength', true, array(0, 255)),
                     array('EmailAddress')
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'text', 'subject',
            array(
                 'label'       => 'Тема',
                 'placeholder' => 'Тема обсуждения',
                 'required'    => false,
                 'maxlength'   => '255',
                 'validators'  => array(
                     array('StringLength', true, array(0, 255))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'checkbox', 'moderate',
            array(
                 'label'       => 'Модерация',
                 'placeholder' => 'Модерация',
                 'required'    => false
            )
        );


        $this->addElement(
            'button', 'submit',
            array(
                 'label'      => 'Добавить',
                 'type'       => 'submit',
                 'buttonType' => 'success'
            )
        );

        $this->addElement(
            'cancel', 'cancel',
            array(
                 'label'      => 'Отмена',
                 'buttonType' => 'danger',
                 'href' => '/'
            )
        );

        $this->addDisplayGroup(
            array('submit', 'cancel'),
            'actions',
            array(
                 'disableLoadDefaultDecorators' => true,
                 'decorators'                   => array('Actions')
            )
        );


    }
}