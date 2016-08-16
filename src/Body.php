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
class Body extends SubRootElement
{
	public function __construct(Document $dom)
	{
		parent::__construct($dom, 'body');
	}
}
