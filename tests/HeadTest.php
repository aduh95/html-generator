<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\Head;


/**
 * Test class for \aduh95\HTMLGenerator\Head
 * * @link http://phpunit.de/manual/
 */
class HeadTest extends TestCase
{
    /**
     * @covers \aduh95\HTMLGenerator\Head::__construct
     */
    public function testObjectConstructor()
    {
        $return = new Head(new Document);

        $this->assertInstanceOf('aduh95\HTMLGenerator\Head', $return);
        $this->assertInstanceOf('DOMElement', $return);

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\Head::__construct
     * @covers \aduh95\HTMLGenerator\Document::__construct
     */
    public function testDocumentCreateHead()
    {
        $Document = new Document;

        $this->assertInstanceOf('aduh95\HTMLGenerator\Head', $Document->getHead());
        return $Document->getHead();
    }

    /**
     * @covers \aduh95\HTMLGenerator\Head::__invoke
     * @depends testObjectConstructor
     */
    public function testInvokeObjectToGetItself($head)
    {
        $this->assertInstanceOf('aduh95\HTMLGenerator\Head', $head());
        $this->assertSame($head, $head());
    }

    /**
     * @covers \aduh95\HTMLGenerator\Head::meta
     * @depends testDocumentCreateHead
     */
    public function testMetaManipulation($head)
    {
        $metaName = 'test01';
        $metaValue = 'value';
        $metaValue2 = 'value2';

        // Test if a meta exists
        $this->assertNull($head->meta($metaName), 'The <meta> already exists');

        // Test adding the meta
        $count = $head->childNodes->length;
        $this->assertInstanceOf('aduh95\HTMLGenerator\Head', $head->meta($metaName, $metaValue));
        $this->assertSame($count + 1, $head->childNodes->length, 'The <meta> hasn\'t been added');

        // Test retriving value
        $this->assertSame($head->meta($metaName), $metaValue, 'Cannot retrieve <meta>\'s content');

        // Test modifying the value
        $this->assertInstanceOf('aduh95\HTMLGenerator\Head', $head->meta($metaName, $metaValue2));
        $this->assertSame($count + 1, $head->childNodes->length);
        $this->assertSame($head->meta($metaName), $metaValue2, 'The <meta>\'s content hasn\'t been changed');

        // Test removing the meta
        $this->assertInstanceOf('aduh95\HTMLGenerator\Head', $head->removeMeta($metaName));
        $this->assertSame($count, $head->childNodes->length, 'The <meta> hasn\'t been removed');
        $this->assertNull($head->meta($metaName), 'The <meta> already exists');
    }

    /**
     * @covers \aduh95\HTMLGenerator\Head::script
     * @depends testDocumentCreateHead
     */
    public function testScriptAdding($head)
    {
        $head->script('//example.com/script.js');

        $this->assertSame('script', $head->lastChild->tagName);
        $this->assertTrue($head->lastChild->hasAttribute('src'));
        $this->assertTrue($head->lastChild->hasAttribute('defer'));
    }

    /**
     * @covers \aduh95\HTMLGenerator\Head::style
     * @depends testDocumentCreateHead
     */
    public function testStyleAdding($head)
    {
        $head->style('//example.com/style.css');

        $this->assertSame('link', $head->lastChild->tagName);
        $this->assertTrue($head->lastChild->hasAttribute('rel'));
        $this->assertTrue($head->lastChild->hasAttribute('href'));
    }
}
