<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 12.09.13
 * Time: 23:51
 * To change this template use File | Settings | File Templates.
 */

class Twitter_Bootstrap_Form_Element_Cancel extends Twitter_Bootstrap_Form_Element_Submit
{
    /**
     * Use formButton view helper by default
     *
     * @var string
     */
    public $helper = 'formCancel';

    protected $href = '';

    /**
     * @param string $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Class constructor
     *
     * @param       $spec
     * @param array $options
     */
    public function __construct($spec, $options = null)
    {
        if (isset($options['href'])) {
            $this->href = $options['href'];
        }

        if (!isset($options['class'])) {
            $options['class'] = '';
        }

        $classes = explode(' ', $options['class']);
        $classes[] = 'btn';
        $classes[] = 'btn-' . Twitter_Bootstrap_Form_Element_Submit::BUTTON_DANGER;

        if (isset($options['disabled'])) {
            $classes[] = 'disabled';
        }

        $classes = array_unique($classes);

        $options['class'] = trim(implode(' ', $classes));

        parent::__construct($spec, $options);
    }

}