<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\HTMLElement;


/**
 * Test class for \aduh95\HTMLGenerator\BodyElement
 * * @link http://phpunit.de/manual/
 */
class HTMLElementTest extends TestCase
{
    /** @var \aduh95\HTMLGenerator\Document */
    protected $document;

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::__construct
     * @covers \aduh95\HTMLGenerator\HTMLElement::append
     */
    public function testObjectConstructor()
    {
        $this->document = new Document;
        $return = ($this->document)()->append($this->document->createElement('div'));

        $this->assertInstanceOf('aduh95\HTMLGenerator\HTMLElement', $return);
        $this->assertInstanceOf('DOMElement', $return);

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::text
     * @depends testObjectConstructor
     */
    public function testAddingText($div)
    {
        $this->assertFalse($div->getDOMElement()->hasChildNodes());
        $div->text('test');
        $this->assertTrue($div->getDOMElement()->hasChildNodes());
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::attr
     * @depends testObjectConstructor
     */
    public function testAddingAttribute($div)
    {
        $this->assertFalse($div->getDOMElement()->hasAttribute('test'));
        $div->attr('test', 'value');
        $this->assertTrue($div->getDOMElement()->hasAttribute('test'));
        $this->assertSame($div->getDOMElement()->getAttribute('test'), 'value');

        return $div;
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::attr
     * @depends testObjectConstructor
     */
    public function testAddingBooleanAttribute($div)
    {
        $this->assertFalse($div->getDOMElement()->hasAttribute('testBool'));
        $div->attr('testBool', true);
        $this->assertTrue($div->getDOMElement()->hasAttribute('testBool'));
        $this->assertSame($div->getDOMElement()->getAttribute('testBool'), 'testBool');
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::attr
     * @depends testAddingAttribute
     */
    public function testRemovingAttribute($div)
    {
        $div->attr('test', false);
        $this->assertFalse($div->getDOMElement()->hasAttribute('test'));
    }
}
