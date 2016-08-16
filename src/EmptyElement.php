<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;
use DOMNode;

/**
 * Represents an empty element
 * @author aduh95
 */
class EmptyElement extends DOMNode
{
    public $parentElement;

    /**
     * Does not contain any DOMElement
     * @return HTMLElement $parent
     */
    public function __invoke()
    {
        return $this->parentElement;
    }

    /**
     * Does not contain any DOMElement
     * @return null
     */
    public function getDOMElement()
    {
        return null;
    }

    public function is($tagName)
    {
        return false;
    }
}
