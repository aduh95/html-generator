<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\Body;


/**
 * Test class for \aduh95\HTMLGenerator\Body
 * * @link http://phpunit.de/manual/
 */
class BodyTest extends TestCase
{
    /**
     * @covers \aduh95\HTMLGenerator\Body::__construct
     */
    public function testObjectConstructor()
    {
        $return = new Body(new Document);

        $this->assertInstanceOf('aduh95\HTMLGenerator\Body', $return);
        $this->assertInstanceOf('DOMElement', $return);

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\Body::__invoke
     * @depends testObjectConstructor
     */
    public function testInvokeObjectToGetItself($body)
    {
        $this->assertInstanceOf('aduh95\HTMLGenerator\Body', $body());
        $this->assertSame($body, $body());
    }
}
