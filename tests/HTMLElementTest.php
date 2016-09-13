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
    protected static $document;

    /**
     * @return \aduh95\HTMLGenerator\Document
     */
    public function getDocument()
    {
        if (!isset(self::$document)) {
            self::$document = new Document;
        }
        return self::$document;
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::__construct
     * @covers \aduh95\HTMLGenerator\HTMLElement::append
     */
    public function testObjectConstructor()
    {
        $return = $this->getDocument()()->append($this->getDocument()->createElement('div'));

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
        $div->empty();
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
        $attrName = 'test';
        $attrValue = 'value';
        $attrValue2 = 'value2';

        $this->assertFalse($div->getDOMElement()->hasAttribute($attrName));
        $div->attr($attrName, $attrValue);
        $this->assertTrue($div->getDOMElement()->hasAttribute($attrName), 'The attribute hasn\'t been created');
        $this->assertSame($div->getDOMElement()->getAttribute($attrName), $attrValue, 'The attribute\'s value is wrong');

        // Test reset the value
        $div->attr($attrName, $attrValue2);
        $this->assertTrue($div->getDOMElement()->hasAttribute($attrName), 'The attribute has been deleted when modiying');
        $this->assertSame($div->getDOMElement()->getAttribute($attrName), $attrValue2, 'The attribute\'s value hasn\'t been changed');

        return $attrName;
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::removeAttr
     * @depends testObjectConstructor
     * @depends testAddingAttribute
     */
    public function testRemovingAttribute($div, $attrName)
    {
        $this->assertTrue($div->getDOMElement()->hasAttribute($attrName));
        $div->removeAttr($attrName);
        $this->assertFalse($div->getDOMElement()->hasAttribute($attrName));
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::attr
     * @depends testObjectConstructor
     */
    public function testAddingBooleanAttribute($div)
    {
        $attrName = 'testBool';

        $this->assertFalse($div->getDOMElement()->hasAttribute($attrName));
        $div->attr($attrName, true);
        $this->assertTrue($div->getDOMElement()->hasAttribute($attrName));
        $this->assertSame($div->getDOMElement()->getAttribute($attrName), $attrName);

        return $attrName;
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::attr
     * @depends testObjectConstructor
     * @depends testAddingBooleanAttribute
     */
    public function testRemovingBooleanAttribute($div, $attrName)
    {
        $div->attr('testBool', false);
        $this->assertFalse($div->getDOMElement()->hasAttribute('testBool'));
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
        $tagName = 'someElement'.rand();
        $length = $HTML->childNodes->length;

        $HTML->append($this->getDocument()->createElement($tagName));
        $this->assertTrue($HTML->hasChildNodes());
        $this->assertStringEndsWith('/'.$tagName, $HTML->lastChild->getNodePath());
        $this->assertSame($HTML->childNodes->length, $length + 1);
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::append
     * @depends testObjectConstructor
     */
    public function testAppendOneElementUsingMagicMethod($HTML)
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
        $HTML->append('<p></p>');

        $this->assertTrue($HTML->hasChildNodes());
        $this->assertStringEndsWith('/p', $HTML->lastChild->getNodePath());
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

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::data
     * @depends testObjectConstructor
     */
    public function testPrendingOneElement($HTML)
    {
        $document = $this->getDocument();

        $element = $HTML->empty()->prepend($document->createElement('secondElement'));
        $this->assertInstanceOf('aduh95\HTMLGenerator\HTMLElement', $element);
        $this->assertTrue($HTML->getDOMElement()->hasChildNodes());
        $this->assertStringEndsWith('/secondElement', $element->getNodePath());
        $this->assertSame($element->getDOMElement(), $HTML->getDOMElement()->firstChild);

        $element = $HTML->empty()->prepend($document->createElement('firstElement'));
        $this->assertInstanceOf('aduh95\HTMLGenerator\HTMLElement', $element);
        $this->assertTrue($HTML->getDOMElement()->hasChildNodes());
        $this->assertStringEndsWith('/firstElement', $element->getNodePath());
        $this->assertSame($element->getDOMElement(), $HTML->getDOMElement()->firstChild);
    }

    /**
     * @covers \aduh95\HTMLGenerator\HTMLElement::video
     * @depends testObjectConstructor
     */
    public function testVideo($HTML)
    {
        $video = $HTML->video();

        $this->assertInstanceOf('aduh95\HTMLGenerator\HTMLElement', $video);
        $this->assertInstanceOf('DOMElement', $video);
        $this->assertFalse($video->hasChildNodes());
        $this->assertSame($video->tagName, 'video');

        $video = $HTML->video(['src'=>'test']);
        
        $this->assertFalse($video->hasChildNodes());
        $this->assertTrue($video->hasAttribute('src'));
        $this->assertSame($video->getAttribute('src'), 'test');
        
        $video = $HTML->video(['src'=>['test', 'other']]);
        
        $this->assertFalse($video->hasAttribute('src'));
        $this->assertTrue($video->hasChildNodes());
        $this->assertSame($video->firstChild->tagName, 'source');
        $this->assertTrue($video->firstChild->hasAttribute('src'));
        $this->assertSame($video->firstChild->getAttribute('src'), 'test');
        $this->assertSame($video->firstChild->nextSibling->tagName, 'source');
        $this->assertTrue($video->firstChild->nextSibling->hasAttribute('src'));
        $this->assertFalse($video->firstChild->nextSibling->hasAttribute('type'));
        $this->assertSame($video->firstChild->nextSibling->getAttribute('src'), 'other');
        
        $video = $HTML->video(['src'=>['video/some'=>'test', 'video/ogg'=>'other']]);
        
        $this->assertFalse($video->hasAttribute('src'));
        $this->assertTrue($video->hasChildNodes());
        $this->assertSame($video->firstChild->tagName, 'source');
        $this->assertTrue($video->firstChild->hasAttribute('src'));
        $this->assertTrue($video->firstChild->hasAttribute('type'));
        $this->assertSame($video->firstChild->getAttribute('src'), 'test');
        $this->assertSame($video->firstChild->nextSibling->tagName, 'source');
        $this->assertTrue($video->firstChild->nextSibling->hasAttribute('src'));
        $this->assertTrue($video->firstChild->nextSibling->hasAttribute('type'));
        $this->assertSame($video->firstChild->nextSibling->getAttribute('type'), 'video/ogg');
        $this->assertSame($video->firstChild->nextSibling->getAttribute('src'), 'other');
    }
}
