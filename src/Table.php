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
    /** @var HTMLElement The <caption> element of this table */
    protected $caption;

    /** @var HTMLElement The <thead> element of this table */
    protected $thead;
    /** @var HTMLElement The <tfoot> element of this table */
    protected $tfoot;

    /** @var HTMLElement The <tbody> element of this table */
    protected $tbody;

    /** @var int A bitmask of the options for this table */
    protected $options;

    /** @var int The number of rows to display the <tfoot> */
    protected $autoRows;

    const
        /** @var int Option to not have any <tfoot> */
        NO_TFOOT = 1,
        /** @var int Option to have same content in <tfoot> and <thead> */
        TFOOT_EQUALS_THEAD = 1<<1,
        /**
         * @var int Default option, the <tfoot> will be cloned from the <thead>
         *          if the number of rows exceeds the number of rows given in
         *          the constructor.
         */
        AUTO_TFOOT = 1<<2,
        /** @var int Option to have the first cell of each row as <th> and not <td> */
        ROWED = 1<<3;


    /**
     * @param array $attr The attributes of the table
     * @param int $options A bitmask of one or more of the options above
     * @param int $autoRows The number of rows to display the <tfoot> if the auto option is selected
     */
    public function __construct(Document $dom, $options = self::AUTO_TFOOT, $autoRows = 10)
    {
        parent::__construct($dom, 'table');

        $this->tbody = $this->document->createElement('tbody');
        parent::append($this->tbody);

        $this->options = $options;
        $this->autoRows = $autoRows;
    }

    /**
     * Set the <caption> of this <table>
     * @return self instance
     */
    public function caption($content = null)
    {
        if ($content === null && isset($this->caption)) {
            $this->getDOMElement()->removeChild($this->caption);
        } elseif (isset($this->caption)) {
            $this->caption->empty()->text($content);
        } else {
            $this->caption = parent::prepend($this->document->createElement('caption'))->text($content);
        }

        return $this;
    }

    /**
     * @return boolean Returns if the <tfoot> is supposed to be cloned from <thead>
     */
    protected function autoTFoot()
    {
        if (
            ($this->options & self::AUTO_TFOOT && $this->tbody->childNodes->length > $this->autoRows) ||
            $this->options & self::TFOOT_EQUALS_THEAD
        ) {
            $tfoot = $this->getTFoot()->empty();
            foreach ($this->thead->childNodes as $child) {
                $tfoot->append($child->cloneNode(true));
            }
        }
    }

    /**
     * Creates the <thead> element if it does not exist yet
     * @return HTMLElement The <thead> element of this table
     */
    public function getTHead()
    {
        if ($this->thead === null) {
            $this->thead = parent::prepend($this->document->createElement('thead'));
            if (isset($this->caption)) {
                $this->getDOMElement()->insertBefore($this->caption, $this->thead);
            }
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
        $theadRow = $this->getTHead()->empty()->tr();

        foreach ($th as $value) {
            $theadRow->th()->text($value);
        }
        $this->autoTFoot();

        return $this;
    }

    /**
     * Add table head, including content as raw (XSS possible)
     * @param string[] $th The HTML values of the table head cells
     * @return static instance
     */
    public function theadRaw($th)
    {
        $this->getTHead()->empty()->tr()->append(array_map(function ($value) {
            return new HTMLElement($this->document, 'th', $value);
        }, $th));


        $this->autoTFoot();

        return $this;
    }

    /**
     * Creates the <tfoot> element if it does not exist yet
     * @return HTMLElement The <tfoot> element of this table
     */
    public function getTFoot()
    {
        if ($this->tfoot === null) {
            $this->tfoot = $this->getDOMElement()->insertBefore($this->document->createElement('tfoot'), $this->tbody);
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
        $tfootRow = $this->getTFoot()->empty()->tr();

        foreach ($th as $value) {
            $tfootRow->th()->text($value);
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
        $doc = $this->document;
        $this->getTFoot()->empty()->tr()->append(array_map(function ($value) use ($doc) {
            return new HTMLElement($doc, 'th', $value);
        }, $th));

        return $this;
    }

    /**
     * Add a line to the tbody of the current table
     *
     * If you pass an array of string, a DOMNodeList (containing <td> or <th>)
     * object or a DOMElement (a <tr>), one single line will be added.
     * You can also pass an array of one of the previous elements or a
     * DOMNodeList containing <tr> to add several lines.
     * You can also pass several arguments to add several lines.
     *
     * @param string[]|string[][]|\DOMNodeList|\DOMNodeList[]|\DOMElement|\DOMElement[] ...$line The line(s) to add
     * @return self instance
     */
    public function append($line = null)
    {
        switch (func_num_args()) {
            case 0:
                break;

            case 1:
                $newLine = $this->tbody->tr();

                if (is_array($line)) {
                    foreach ($line as $content) {
                        if (is_array($content)) {
                            $this->append($content);
                        } else {
                            $newLine->td()->text($content);
                        }
                    }
                } elseif ($line instanceof DOMElement && strtolower($line->nodeName)==='tr') {
                    $this->tbody->append($line);
                } elseif ($line instanceof DOMNodeList) {
                    foreach ($line as $lineElem) {
                        switch (strtolower($line->nodeName)) {
                            case 'tr':
                                $this->append($lineElem);
                                break;

                            case 'td':
                            case 'th':
                                $newLine->append($lineElem);
                                break;

                            default:
                                $newLine->td()->text($lineElem);
                                break;
                        }
                    }
                }
                if(!$newLine->getDOMElement()->hasChildNodes()) {
                    $newLine->remove();
                }

                if ($this->options & self::AUTO_TFOOT) {
                    $this->autoTFoot();
                }
                break;

            default:
                array_map([$this, __METHOD__], func_get_args());
                break;
        }

        return $this;
    }

    /**
     * Refuses any method name that does not match with a particular table children elements
     * @throws \Exception
     */
    public function __call($name, $value)
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
            $this->append($value);
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
