<?php
/**
 * Created by PhpStorm.
 * User: Moris
 * Date: 03.11.13
 * Time: 22:56
 */

class Application_Form_News_News extends Twitter_Bootstrap_Form_Horizontal
{
    public function init()
    {
        // Указываем метод формы
        $this->setMethod('post');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $this->setIsArray(true);
        $this->setElementsBelongTo('news');

        $this->addElement(
            'text', 'title',
            array(
                 'label'       => 'Название',
                 'placeholder' => 'Заголовок новости',
                 'required'    => true,
                 'maxlength'   => '255',
                 'validators'  => array(
                     array('StringLength', true, array(0, 255))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'text', 'date',
            array(
                 'label'       => 'Дата публикации',
                 'placeholder' => 'Дата публикации',
                 'required'    => true,
                 'maxlength'   => '20',
                 'class'       => 'datepicker',
                 'validators'  => array(
                     array('StringLength', true, array(0, 255))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'select', 'category',
            array('label'    => 'Категория',
                  'required' => true
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

        //$this->getElement('file')->addDecorator()
        //print_r($this->getElement('file')->getDecorator('Addon')->setOption('content', 'asfasfasfa'));

        //print_r($this->getElement('file')->addDecorator('HtmlTag', array('tag'=>'img', 'placement'=>'prepend', 'src'=>'/images/_main.jpg')));

        //if ($this->news->file->getName()) { <a href="/files<?php echo $this->escape($this->news->file->getSubPath());/<?php echo $this->escape($this->news->file->getName()); " target="_blank">загрузить</a> }

        $this->addElement(
            'textarea', 'short_text',
            array(
                 'label'       => 'Анонс',
                 'placeholder' => 'Анонс',
                 'required'    => true,
                 'validators'  => array(
                     array('StringLength', true, array(0, 5000))
                 ),
                 'filters'     => array('StringTrim', 'StripTags')
            )
        );

        $this->addElement(
            'textarea', 'full_text',
            array(
                 'label'       => 'Полный текст',
                 'placeholder' => 'Полный текст новости',
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

    public function setCategoryList($categoryList)
    {
        if (!empty($categoryList) && $categoryList != false) {
            $element = $this->getElement('category');

            foreach ($categoryList as $category) {
                $element->addMultiOption($category->getId(), $category->getTitle());
            }
        }
    }

} 