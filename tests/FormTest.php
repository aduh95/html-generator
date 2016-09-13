<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;
use aduh95\HTMLGenerator\Form;


/**
 * Test class for \aduh95\HTMLGenerator\Form
 * @link http://phpunit.de/manual/
 */
class FormTest extends TestCase
{
    /**
     * @covers \aduh95\HTMLGenerator\Form::__construct
     */
    public function testObjectConstructor()
    {
        $return = new Form(new Document);

        $this->assertInstanceOf('aduh95\HTMLGenerator\Form', $return);
        $this->assertInstanceOf('DOMElement', $return);

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\Form::fieldset
     * @depends testObjectConstructor
     */
    public function testFieldsetWithoutLegend($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());
        $fieldset = $form->fieldset();

        $this->assertTrue($form->hasChildNodes());
        $this->assertSame($form->firstChild->tagName, 'fieldset');
        $this->assertFalse($form->firstChild->hasChildNodes());
    }

    /**
     * @covers \aduh95\HTMLGenerator\Form::fieldset
     * @depends testObjectConstructor
     */
    public function testFieldsetWithLegend($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());
        $fieldset = $form->fieldset('test');

        $this->assertTrue($form->hasChildNodes());
        $this->assertSame($form->firstChild->tagName, 'fieldset');
        $this->assertTrue($form->firstChild->hasChildNodes());
        $this->assertSame($form->firstChild->firstChild->tagName, 'legend');
        $this->assertSame($form->firstChild->firstChild->textContent, 'test');
    }

    /**
     * @covers \aduh95\HTMLGenerator\Form::input
     * @covers \aduh95\HTMLGenerator\HTMLElement::input
     * @depends testObjectConstructor
     */
    public function testInput($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());
        $fieldset = $form->input();

        $this->assertTrue($form->hasChildNodes());
        $this->assertSame($form->firstChild->tagName, 'fieldset');
        $this->assertTrue($form->firstChild->hasChildNodes());
        $this->assertSame($form->firstChild->firstChild->tagName, 'div');
        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'input');
        $this->assertTrue($form->firstChild->firstChild->firstChild->hasAttribute('type'));
        $this->assertSame($form->firstChild->firstChild->firstChild->getAttribute('type'), 'text');
    }
}
