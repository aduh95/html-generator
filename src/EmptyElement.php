<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use DOMText;

/**
 * Represents an empty element
 * @author aduh95
 */
class EmptyElement extends DOMText
{
    /**
     * Overrides parent's constructor
     */
    public function __construct()
    {
        parent::__construct('');
    }
}
