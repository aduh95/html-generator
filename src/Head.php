<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

/**
 * Represents a HTML head node
 * @author aduh95
 * @api
 */
class Head extends HTMLElement
{
	public function __construct(Document $dom)
	{
		parent::__construct($dom, 'head');
        $this->parentElement = $dom->getDOMDocument()->documentElement;
	}
}
