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
     * @covers \aduh95\HTMLGenerator\Form::fieldset
     * @covers \aduh95\HTMLGenerator\Form::input
     * @covers \aduh95\HTMLGenerator\HTMLElement::input
     * @depends testObjectConstructor
     */
    public function testInput($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());
        $input = $form->input();

        $this->assertTrue($form->hasChildNodes());
        $this->assertSame($form->firstChild->tagName, 'fieldset');
        $this->assertTrue($form->firstChild->hasChildNodes());
        $this->assertSame($form->firstChild->firstChild->tagName, 'div');
        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'input');
        $this->assertTrue($form->firstChild->firstChild->firstChild->hasAttribute('type'));
        $this->assertSame($form->firstChild->firstChild->firstChild->getAttribute('type'), 'text');
    }

    /**
     * @covers \aduh95\HTMLGenerator\Form::input
     * @covers \aduh95\HTMLGenerator\HTMLElement::input
     * @depends testObjectConstructor
     */
    public function testInputWithLabel($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());
        $input = $form->input(['label'=>'test']);

        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'label');
        $this->assertTrue($form->firstChild->firstChild->firstChild->hasAttribute('for'));
        $this->assertSame($form->firstChild->firstChild->firstChild->textContent, 'test');
        $this->assertSame($form->firstChild->firstChild->firstChild->nextSibling->tagName, 'input');
        $this->assertTrue($form->firstChild->firstChild->firstChild->nextSibling->hasAttribute('id'));
        $this->assertSame(
            $form->firstChild->firstChild->firstChild->getAttribute('for'),
            $form->firstChild->firstChild->firstChild->nextSibling->getAttribute('id'),
            'The <label> and the <input> elements are not linked'
        );
        

        $form->empty();
        $input = $form->input(['label'=>'test', 'type'=>'checkbox']);

        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'label');
        $this->assertSame($form->firstChild->firstChild->firstChild->textContent, 'test');
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->tagName, 'input');
    }

    /**
     * @covers \aduh95\HTMLGenerator\Form::input
     * @covers \aduh95\HTMLGenerator\HTMLElement::input
     * @depends testObjectConstructor
     */
    public function testSelectInput($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());

        $input = $form->input(['type'=>'select']);
        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'select');
        $this->assertFalse($form->firstChild->firstChild->firstChild->hasChildNodes());

        // Test adding <option> elements
        $form->empty();
        $input = $form->input(['type'=>'select', 'options'=>['test']]);

        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'select');
        $this->assertTrue($form->firstChild->firstChild->firstChild->hasChildNodes());
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->tagName, 'option');
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->textContent, 'test');
        $this->assertTrue($form->firstChild->firstChild->firstChild->firstChild->hasAttribute('value'));
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->getAttribute('value'), '0');
        

        // Test <optgroup> support
        $form->empty();
        $input = $form->input(['type'=>'select', 'options'=>['option group'=>['test']]]);

        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'select');
        $this->assertTrue($form->firstChild->firstChild->firstChild->hasChildNodes());
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->tagName, 'optgroup');
        $this->assertTrue($form->firstChild->firstChild->firstChild->firstChild->hasAttribute('label'));
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->getAttribute('label'), 'option group');
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->firstChild->tagName, 'option');
        $this->assertSame($form->firstChild->firstChild->firstChild->firstChild->firstChild->textContent, 'test');
    }

    /**
     * @covers \aduh95\HTMLGenerator\Form::input
     * @covers \aduh95\HTMLGenerator\HTMLElement::input
     * @depends testObjectConstructor
     */
    public function testDatalist($form)
    {
        $form->empty();

        $this->assertFalse($form->hasChildNodes());

        $input = $form->input(['list'=>['test']]);
        $this->assertSame($form->firstChild->firstChild->firstChild->tagName, 'input');
        $this->assertSame($form->firstChild->firstChild->firstChild->nextSibling->tagName, 'datalist');
        $this->assertTrue($form->firstChild->firstChild->firstChild->nextSibling->hasChildNodes());
        $this->assertSame($form->firstChild->firstChild->firstChild->nextSibling->firstChild->tagName, 'option');
        $this->assertSame($form->firstChild->firstChild->firstChild->nextSibling->firstChild->textContent, 'test');
        
        $this->assertTrue($form->firstChild->firstChild->firstChild->hasAttribute('list'));
        $this->assertTrue($form->firstChild->firstChild->firstChild->nextSibling->hasAttribute('id'));
        $this->assertSame(
            $form->firstChild->firstChild->firstChild->getAttribute('list'),
            $form->firstChild->firstChild->firstChild->nextSibling->getAttribute('id'),
            'The <datalist> and the <input> elements are not linked'
        );
    }
}
