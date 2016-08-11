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

    /**
     * @test
     */
    public function testWholeTableCreation()
    {
        $doc = new Document;
        $table = $doc()->table();

        $headings = ['first <b>set</b>', 'noXSS'];
        $table->thead($headings)->tfootRaw($headings);

        $table[]= ['I gat powa', 'I am a powerful tool.'];
        $table[]= ['HTML&amp;Cie.', 'UTF-8: ♥'];

        if (!is_file(Table\WHOLE_TABLE_HTML)) {
            $fileHandler = fopen(Table\WHOLE_TABLE_HTML, 'w');
            fwrite($fileHandler, $doc);
            fclose($fileHandler);
            $this->assertTrue(false, 'The file did not exist, it has been created');
        }
        $this->assertStringEqualsFile(Table\WHOLE_TABLE_HTML, strval($doc));
    }
}
