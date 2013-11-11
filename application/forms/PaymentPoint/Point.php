<?php
/**
 * Created by PhpStorm.
 * User: Moris
 * Date: 03.11.13
 * Time: 22:56
 */

class Application_Form_PaymentPoint_Point extends Twitter_Bootstrap_Form_Horizontal
{
    public function init()
    {
        // Указываем метод формы
        $this->setMethod('post');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $this->setIsArray(true);
        $this->setElementsBelongTo('point');

        $this->addElement(
            'text', 'title',
            array(
                 'label'       => 'Название',
                 'placeholder' => 'Название точки приема платежей',
                 'required'    => true,
                 'maxlength'   => '255',
                 'validators'  => array(
                     array('StringLength', true, array(0, 255))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'file', 'file',
            array(
                 'label'       => 'Файл',
                 'placeholder' => 'Файл',
                 'required'    => false
            )
        );

        $this->getElement('file')->getDecorator('Description')->setOptions(array('escape' => false, 'placement' => Zend_Form_Decorator_Abstract::PREPEND));

        $this->addElement(
            'text', 'address',
            array(
                 'label'       => 'Адрес',
                 'placeholder' => 'Адрес точки платежей',
                 'required'    => true,
                 'validators'  => array(
                     array('StringLength', true, array(0, 255))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'textarea', 'full_text',
            array(
                 'label'       => 'Описание',
                 'placeholder' => 'Описание точки приема платежей',
                 'required'    => false,
                 'validators'  => array(
                     array('StringLength', true, array(0, 500000))
                 ),
                 'filters'     => array('StringTrim')
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
                 'href'       => '/'
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

    public function setImage($fileName, $title = '')
    {
        $this->getElement('file')->setDescription('<img src="/files' . $fileName . '" alt="' . $title . '" />');
    }

} 