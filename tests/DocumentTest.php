<?php
/**
 * @author aduh95
 */

namespace aduh95\HTMLGenerator\tests;


use PHPUnit\Framework\TestCase;

use aduh95\HTMLGenerator\Document;


/**
 * Test class for \aduh95\HTMLGenerator\Document
 * * @link http://phpunit.de/manual/
 */
class DocumentTest extends TestCase
{
    /**
     * @covers \aduh95\HTMLGenerator\Document::__construct
     */
    public function testObjectConstructor()
    {
        $return = new Document;

        $this->assertInstanceOf('aduh95\HTMLGenerator\Document', $return);
        $this->assertInstanceOf('DOMDocument', $return->getDOMDocument());

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\Document::__toString
     * @dataProvider stringValuesProvider
     */
    public function testStringValues($document, $file)
    {
        if (!is_file($file)) {
            $fileHandler = fopen($file, 'w');
            fwrite($fileHandler, $document);
            fclose($fileHandler);
            $this->assertTrue(false, 'The file did not exist, it has been created');
        }
        $this->assertStringEqualsFile($file, strval($document));
    }

    public function stringValuesProvider()
    {
        return array_merge(
            $this->simpleDocumentsValuesProvider(),
            $this->wholeDocumentsValuesProvider()
        );
    }

    public function simpleDocumentsValuesProvider()
    {
        return [
            [new Document, Document\MINIMAL_STRING_VALUE],
            [new Document('titre', 'fr'), Document\FRENCH_STRING_VALUE],
            [new Document('title', 'en', ENT_HTML401), Document\HTML4_STRING_VALUE],
        ];
    }

    public function wholeDocumentsValuesProvider()
    {
        $return = array();

        $return[0]= [new Document, Document\HEAD_MODIFIED_HTML];
        $return[0][0]->getHead()->appendChild($return[0][0]->createElement('meta'))['charset'] = 'uft&amp;8';

        $return[1]= [new Document, Document\BODY_MODIFIED_HTML];
        $return[1][0]()->text('Tes&t');

        return $return;
    }

    /**
     * @covers \aduh95\HTMLGenerator\Document::__invoke
     * @depends testObjectConstructor
     */
    public function testInvokeObjectToGetBodyElement($document)
    {
        $body = $document();
        $this->assertInstanceOf('aduh95\HTMLGenerator\Body', $body);

        return $body;
    }

    /**
     * @depends testInvokeObjectToGetBodyElement
     */
    public function testAddingChildToBodyElement($body)
    {
        $body->div();
    }

    /**
     * @depends testObjectConstructor
     */
    public function testCreatingIDs($doc)
    {
        $this->assertNotEquals($doc->generateID(), $doc->generateID());
    }
}
