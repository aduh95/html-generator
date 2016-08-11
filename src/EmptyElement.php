<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;


/**
 * Represents an empty element
 * @author aduh95
 */
class EmptyElement
{
    public $parentNode;

    /**
     * @param HTMLElement|null $parent The parent node of this element
     */
    public function __construct(HTMLElement $parent = null)
    {
        $this->parentNode = $parent;
    }

    /**
     * Does not contain any DOMElement
     * @return HTMLElement $parent
     */
    public function __invoke()
    {
        return $this->parentNode;
    }

    /**
     * Does not contain any DOMElement
     * @return null
     */
    public function getDOMElement()
    {
        return null;
    }
}
