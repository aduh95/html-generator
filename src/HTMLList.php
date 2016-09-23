<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use DOMElement;
use DOMNodeList;
use Countable;

/**
 * Represents a HTML list (such as `<ul>` or `<ol>`) element
 * @author aduh95
 * @api
 */
class HTMLList extends HTMLElement implements Countable
{
    /**
     * Create a li node as a child of the current list.
     * This method has the following signature:
     * - HTMLElement List::li([array $attributes,] [string ...$rawContent])
     *  @return HTMLElement
     */
    public function li()
    {
        return parent::__call('li', func_get_args());
        // return call_user_func_array('parent::__call', func_get_args());
    }

    /**
     * Add a list item with text content
     * @param string $text The text xontained in the item
     * @return self instance
     */
    public function text($text = null)
    {
        return $this->li()->text($text)->parent();
    }

    /**
     * Add one or several list item(s)
     *
     * If you pass an array of string, a DOMNodeList (containing <li>)
     * or an array of DOMElement <li>, several items will be added.
     * You can also pass several arguments to add several lines.
     * If you pass a string, an item with the string as raw XML content wil be added
     * If you pass a DOMText object, one item will be added as well
     *
     * @param string|string[]|\DOMNodeList|\DOMElement|\DOMElement[] ...$item The item(s) to add
     * @return self instance
     */
    public function append($item = null)
    {
        switch (func_num_args()) {
            case 0:
                return parent::append();
                break;

            case 1:
                if (is_array($item)) {
                    foreach ($item as $content) {
                        if (is_array($content)) {
                            $this->append($content);
                        } else {
                            $this->text($content);
                        }
                    }
                } elseif ($item instanceof DOMElement && strtolower($item->nodeName)==='li') {
                    parent::append($item);
                } elseif ($item instanceof DOMNodeList) {
                    foreach ($item as $lineElem) {
                        if (strtolower($item->nodeName)==='li') {
                            $this->append($lineElem);
                        } else {
                            $this->li()->append($lineElem);
                        }
                    }
                } elseif ($item instanceof EmptyElement) {
                    return parent::append($item);
                } else {
                    $this->li()->append($item);
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
     * Checks if an item exists at this particular index given as parameter
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

    /**
     * Returns the number of items in the current list
     * @return int
     */
    public function count()
    {
        return $this->childNodes->length;
    }
}
