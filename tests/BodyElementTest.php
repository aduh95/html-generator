<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\BodyElement;


/**
 * Test class for \aduh95\HTMLGenerator\BodyElement
 * * @link http://phpunit.de/manual/
 */
class BodyElementTest extends TestCase
{
    /**
     * @covers \aduh95\HTMLGenerator\BodyElement::__construct
     */
    public function testObjectConstructor()
    {
        $return = new BodyElement(new Document);

        $this->assertInstanceOf('aduh95\HTMLGenerator\BodyElement', $return);
        $this->assertInstanceOf('DOMElement', $return);

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\BodyElement::__invoke
     * @depends testObjectConstructor
     */
    public function testInvokeObjectToGetItself($body)
    {
        $this->assertInstanceOf('aduh95\HTMLGenerator\BodyElement', $body());
        $this->assertSame($body, $body());
    }
}
