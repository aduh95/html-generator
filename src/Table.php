<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

/**
 * Represents a HTML table element
 * @author aduh95
 * @api
 */
class Table extends HTMLElement
{
    /** @var HTMLElement The <tbody> element of this table */
    protected $tbody;

    /** @var HTMLElement The <thead> element of this table */
    protected $thead;
    /** @var HTMLElement The <tfoot> element of this table */
    protected $tfoot;

    const   NO_TFOOT=           1,
            TFOOT_EQUALS_THEAD= 1<<1,
            AUTO =              1<<2,
            TBODY_NO_XSS =      1<<3,
            TITLES_NO_XSS =     1<<4,
            DATATABLE  =        1<<5,
            TABLELINK  =        1<<6,
            NO_DEFAULT_CLASSES= 1<<7,
            ROWED =             1<<8;

    /**
     * @param array $attr The attributes of the table
     */
    public function __construct(Document $dom)
    {
        parent::__construct($dom, 'table');
    }

    public function getTHead()
    {
        if ($this->thead === null) {
            $this->thead = $this->prepend($this->document->createElement('thead'));
        }

        return $this->thead;
    }

    /**
     * Add table head (no XSS possible)
     * @param string[] $th The text values of the table head cells
     * @return static instance
     */
    public function thead($th)
    {
        $thead = $this->getTHead()->empty();

        foreach ($th as $value) {
            $thead->th()->text($value);
        }

        return $this;
    }

    /**
     * Add table head, including content as raw (XSS possible)
     * @param string[] $th The HTML values of the table head cells
     * @return static instance
     */
    public function theadRaw($th)
    {
        $this->getTHead()->empty()->append(array_map(function ($value) {
            return new HTMLElement($this->document, 'th', $value);
        }, $th));

        return $this;
    }

    public function getTFoot()
    {
        if ($this->tfoot === null) {
            $this->tfoot = $this->prepend($this->document->createElement('tfoot'));
        }

        return $this->tfoot;
    }

    /**
     * Add table foot (no XSS possible)
     * @param string[] $th The text values of the table foot cells
     * @return static instance
     */
    public function tfoot($th)
    {
        $tfoot = $this->getTFoot()->empty();

        foreach ($th as $value) {
            $tfoot->th()->text($value);
        }

        return $this;
    }

    /**
     * Add table foot, including content as raw (XSS possible)
     * @param string[] $th The HTML values of the table foot cells
     * @return static instance
     */
    public function tfootRaw($th)
    {
        $this->getTFoot()->empty()->append(array_map(function ($value) {
            return new HTMLElement($this->document, 'th', $value);
        }, $th));

        return $this;
    }

    /**
     * Refuses any method name that does not match with a particular table children elements
     * @throws \Exception
     */
    public function __call()
    {
        throw new \Exception('Invalid tag name');
    }

    /**
     * Checks if a line exists at this particular index given as parameter
     * Checks if an attribute is set for this tag and not null
     *
     * @param string|int $line The index of the line
     * @return boolean The result of the test
     */
    public function offsetExists($line)
    {
        return is_string($line) ?
            parent::offsetExists($line) :
            $this->getDOMElement()->lastChild->childNodes->lenght < $line;
    }

    /**
     * Returns the elements in the selected line
     * Returns the value the attribute set for this tag
     *
     * @param string|int $line The index of the line to get
     * @return \DOMElement The list of element in this line
     */
    public function offsetGet($line)
    {
        return is_string($line) ?
            parent::offsetExists($line) :
            $this->getDOMElement()->lastChild->childNodes->item($line);
    }

    /**
     * Add a line to the current Table
     * Sets the value an attribute for this tag
     *
     * @param null|string $attribute The attribute to set
     * @param mixed $value The value to set
     * @return void
     */
    public function offsetSet($attribute, $value)
    {
        if ($attribute === null) {
            $this->addLine();
        } else {
            return parent::offsetSet($attribute, $value);
        }
    }

    /**
     * Removes a line
     * Removes an attribute
     *
     * @param int|string $line The line to remove
     * @return void
     */
    public function offsetUnset($line)
    {
        if (is_string($line)) {
            parent::offsetExists($line);
        } elseif (is_numeric($line)) {
            $this->tbody->removeChild($this->tbody->childNodes->item($line));
        }
    }
}
