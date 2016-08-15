<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

/**
 * Represents a HTML body node
 * @author aduh95
 * @api
 */
class Body extends HTMLElement
{
	public function __construct(Document $dom)
	{
		parent::__construct($dom, 'body');
        $this->parentElement = $dom->getDOMDocument()->documentElement;
	}

    /**
     * @return self Return the current object
     */
    public function __invoke()
    {
        return $this;
    }
}
