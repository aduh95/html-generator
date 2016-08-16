<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

/**
 * Represents a HTML head or body node
 * @author aduh95
 * @api
 */
abstract class SubRootElement extends HTMLElement
{
    /**
     * Object constructor
     * @param Document $dom @see HTML::__construct
     * @param string $tagName @see HTML::__construct
     */
	public function __construct(Document $dom, $tagName)
	{
		parent::__construct($dom, $tagName);
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
