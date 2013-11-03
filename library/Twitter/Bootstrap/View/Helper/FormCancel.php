<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Moris
 * Date: 12.09.13
 * Time: 23:54
 * To change this template use File | Settings | File Templates.
 */

class Twitter_Bootstrap_View_Helper_FormCancel extends Zend_View_Helper_FormButton
{
    /**
     * Generates a 'file' element.
     *
     * @access public
     *
     * @param string|array $name    If a string, the element name.  If an
     *                              array, all other parameters are ignored, and the array elements
     *                              are extracted in place of added parameters.
     *
     * @param null         $value
     * @param array        $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formCancel($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, id, value, attribs, options, listsep, disable

        $attribs['class'] = trim($attribs['class']);

        // build the element
        $xhtml = '<a href="' . $attribs['href'] . '"' . $this->_htmlAttribs($attribs) .'>' . $this->view->escape($value) . '</a>';

        return $xhtml;
    }


}