<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\Table;


/**
 * Test class for \aduh95\HTMLGenerator\Table
 * * @link http://phpunit.de/manual/
 */
class TableTest extends TestCase
{
    /** @var \aduh95\HTMLGenerator\Document */
    protected $document;

    /**
     * @covers \aduh95\HTMLGenerator\Table::__construct
     */
    public function testObjectConstructor()
    {
        $this->document = new Document;
        $return = ($this->document)()->table();

        $this->assertInstanceOf('aduh95\HTMLGenerator\Table', $return);
        $this->assertInstanceOf('DOMElement', $return);

        return $return;
    }
}
