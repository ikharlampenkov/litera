<?php
/**
 * Created by PhpStorm.
 * User: Moris
 * Date: 03.11.13
 * Time: 22:56
 */

class Application_Form_News_Category extends Twitter_Bootstrap_Form_Horizontal
{
    public function init()
    {
        // Указываем метод формы
        $this->setMethod('post');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $this->setIsArray(true);
        $this->setElementsBelongTo('category');

        $this->addElement(
            'text', 'title',
            array(
                'label' => 'Название',
                'placeholder' => 'Название категории',
                'required' => true,
                'maxlength' => '255',
                'validators' => array(
                    array('StringLength', true, array(0, 255))
                ),
                'filters' => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'button', 'submit',
            array(
                'label' => 'Добавить',
                'type' => 'submit',
                'buttonType' => 'success'
            )
        );

        $this->addElement(
            'cancel', 'cancel',
            array(
                'label' => 'Отмена',
                'buttonType' => 'danger',
                'href' => '/'
            )
        );

        $this->addDisplayGroup(
            array('submit', 'cancel'),
            'actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );
    }
} 