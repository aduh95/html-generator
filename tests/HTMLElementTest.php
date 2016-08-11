<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\HTMLElement;


/**
 * Test class for \aduh95\HTMLGenerator\HTMLElement
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

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::empty
     * @depends testObjectConstructor
     */
    public function testEmptyElement($HTML)
    {
        $HTML->p('Some content');

        $this->assertTrue($HTML->getDOMElement()->hasChildNodes());
        $this->assertSame($HTML->getDOMElement(), $HTML->empty()->getDOMElement());
        $this->assertFalse($HTML->getDOMElement()->hasChildNodes(), 'The element has still children.');
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::append
     * @depends testObjectConstructor
     */
    public function testAppendEmptyElement($HTML)
    {
        $this->assertInstanceOf('aduh95\HTMLGenerator\EmptyElement', $HTML->append());
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::append
     * @depends testObjectConstructor
     */
    public function testAppendOneElement($HTML)
    {
        $element = $HTML->someElement();
        $this->assertInstanceOf('aduh95\HTMLGenerator\HTMLElement', $element);
        $this->assertTrue($HTML->getDOMElement()->hasChildNodes());
        $this->assertStringEndsWith('/someElement', $element->getNodePath());
        $this->assertSame($element->getDOMElement(), $HTML->getDOMElement()->lastChild);
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::append
     * @depends testObjectConstructor
     */
    public function testAppendHTMLString($HTML)
    {
        $element = $HTML->append('<p></p>');
        $this->assertInstanceOf('aduh95\HTMLGenerator\HTMLElement', $element);
        $this->assertTrue($HTML->getDOMElement()->hasChildNodes());
        $this->assertSame('/p', strrchr($element->getNodePath(), '/'));
        $this->assertSame($element->getDOMElement(), $HTML->getDOMElement()->lastChild);
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::data
     * @depends testObjectConstructor
     */
    public function testAddingDataAttribute($HTML)
    {
        $element = $HTML->data('simple', true);
        $this->assertTrue(isset($HTML['data-simple']));
        $this->assertTrue($HTML['data-simple']);

        $element = $HTML->data('HTMLCamelCaseTest', 'value');
        $this->assertTrue($HTML->getDOMElement()->hasAttribute('data-html-camel-case-test'));
        $this->assertSame($HTML->data('HTMLCamelCaseTest'), $HTML->attr('data-html-camel-case-test'));
    }
}
