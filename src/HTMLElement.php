<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use ArrayAccess;
use DOMElement;
use \Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Represents any HTML element
 * @author aduh95
 */
class HTMLElement extends DOMElement implements ArrayAccess
{
	protected $document;
	protected $DOMElement;
	protected $parentElement;

	public function __construct(Document $dom, $tagName, $rawContent = '')
	{
		parent::__construct($tagName, $rawContent);
		$this->document = $dom;
        // $this->DOMElement = $this->getDOMDocument()->createElement($tagName);
        // $dom->getDOMDocument()->importNode($this->getDOMElement());
	}

	/**
	 * Appends a text node at the element
	 * @param string $text The text to input
	 * @return HTMLElement
	 */
	public function text($text = '')
	{
		$this->getDOMElement()->appendChild($this->getDOMDocument()->createTextNode($text));
		return $this;
	}

	public function append(...$elem)
	{
		switch (func_num_args()) {
			case 0:
				return $this->append(new EmptyElement($this->document, 'z'));
				break;

			case 1:
				$elem = array_pop($elem);
				if (is_object($elem) && $elem instanceof self) {
					$return = $this->getDOMElement()->appendChild($elem);
					$return->parentElement = $this;

					return $return;
				} else {
					return HtmlPageCrawler::create($elem)->appendTo($this);
				}
				break;

			default:
				foreach ($elem as $element) {
					if (is_string($element)) {
						if (strncmp($element, '<', 1)) {
							$this->text($element);
						} else {
							$this->append($element);
						}
					}
				}
				return $this;
				break;
		}
	}

    /**
     * (Re)Define an attribute or many attributes
     * @param string|array $attribute
     * @param string $value
     * @return Markup instance
     */
    public function attr($attribute, $value = null)
    {
        if(is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $this[$key] = $value;
            }
        } else {
            $this[$attribute] = $value;
        }
        return $this;
    }

    /**
     * Checks if an attribute is set for this tag and not null
     *
     * @param string $attribute The attribute to test
     * @return boolean The result of the test
     */
    public function offsetExists($attribute)
    {
        return $this->getDOMElement()->hasAttribute($attribute);
    }

    /**
     * Returns the value the attribute set for this tag
     *
     * @param string $attribute The attribute to get
     * @return mixed The stored result in this object
     */
    public function offsetGet($attribute)
    {
        return $this->offsetExists($attribute) ? $this->getDOMElement()->getAttribute($attribute) : null;
    }

    /**
     * Sets the value an attribute for this tag
     *
     * @param string $attribute The attribute to set
     * @param mixed $value The value to set
     * @return void
     */
    public function offsetSet($attribute, $value)
    {
        if ($value===false) {
            $this->offsetUnset($attribute);
        } else {
            if ($value===true) {
                $value = $attribute;
            }
            return $this->getDOMElement()->setAttribute($attribute, $value);
        }
    }

    /**
     * Removes an attribute
     *
     * @param mixed $attribute The attribute to unset
     * @return void
     */
    public function offsetUnset($attribute)
    {
        if ($this->offsetExists($attribute)) {
        	$this->getDOMElement()->removeAttribute($attribute);
        }
    }

    public function __call($tagName, $content)
    {
    	$tag = $this->append(new self($this->document, $tagName));
    	if(count($content) && is_array($content[0])) {
    		$tag->attr(array_shift($content));
    	}
    	$tag->append($content);
    	return $tag;
    }

    public function __invoke()
    {
    	return $this->getDOMElement()->parent();
    }

    /**
     * @return string The HTML value of the element
     */
	public function __toString()
	{
		return HtmlPageCrawler::create($this->getDOMElement())->saveHTML();
	}

	public function parent()
	{
		return $this->parentElement;
	}

	public function getDOMElement()
	{
		return $this;
	}

	protected function getDOMDocument()
	{
		return $this->document->getDOMDocument();
	}
}
