<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

/**
 * Represents a HTML <form> element
 * @author aduh95
 * @api
 */
class Form extends HTMLElement
{
    protected $defaultValues = array();

    /**
     * @param Document $document The document owner of this element
     * @param array $attributes The attributes assigned to this element
     */
    public function __construct($document, $attributes=array())
    {
        parent::__construct($document, 'form');
        $this->attr($attributes);
    }

    /**
     * @param mixed|array $var If not an array, no default value will be set for the inputs
     */
    public function setDefaultValues($var)
    {
        $this->defaultValues = is_array($var) ? $var : array();
    }

    /**
     * Set default value for all the inputs retrieved in the $_POST values
     */
    public function valuesFromPost()
    {
        return $this->setDefaultValues($_POST);
    }

    /**
     * @param string|array $legend The legend of your fieldset, or the attributes of the <legend> element
     * @return HTMLElement The <fieldset> element
     */
    public function fieldset($legend = null)
    {
        $fieldset = parent::fieldset();
        if (isset($legend)) {
            $fieldset->legend($legend);
        }
        return $fieldset;
    }

    /**
     * Creates a <fieldset> added to the current form, puts a <input> into it and returns it
     * @param array $attr
     * @param array $defaultValue
     *
     * @return HTMLElement The <input> element created
     */
    public function input($attr = array(), $defaultValues = array())
    {
        return $this->fieldset()->input($attr, $this->defaultValues);
    }

    /**
     * Returns the default values set for this form
     * @return array
     */
    public function getDefaultValues()
    {
        return $this->defaultValues;
    }
}
