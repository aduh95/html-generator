<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use ArrayAccess;
use DOMNode;
use DOMElement;
use \Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Represents any HTML element
 * @author aduh95
 * @api
 */
class HTMLElement extends DOMElement implements ArrayAccess
{
    /** @var Document The owner document of this element */
	protected $document;

    // protected $DOMElement;

    /** @var \DOMNode The parent node of this element */
	protected $parentElement;

	public function __construct(Document $dom, $tagName, $rawContent = '')
	{
		parent::__construct($tagName);

		$this->document = $dom;
        $dom->getFragment()->appendChild($this);

        if (!empty($rawContent)) {
            $this->append($rawContent);
        }
        // $this->DOMElement = $this->getDOMDocument()->importNode($this);
	}

	/**
	 * Appends a text node at the element, or returns the text value
	 * @param string|null $text The text to input
	 * @return HTMLElement|string
	 */
	public function text($text = null)
	{
        if($text===null) {
            return $this->getDOMElement()->textContent;
        }

		$this->getDOMElement()->appendChild($this->getDOMDocument()->createTextNode($text));
		return $this;
	}

    /**
     * Appends the element(s) in parameter at the end of the current Node
     * @param mixed ...$elem
     * @return self
     */
	public function append($elem = null)
	{
		switch (func_num_args()) {
			case 0:
				return new EmptyElement($this);
				break;

			case 1:
				if (is_object($elem) && $elem instanceof self) {
                    if (!($elem instanceof EmptyElement)) {
					   $return = $this->getDOMElement()->appendChild($elem);
                    }
                    $this->affiliate($return);

					return $return;
				} else {
                    if (empty($elem)) {
                        return $this;
                    } elseif ($this->ownerDocument === null) {
                        $doc = $this->document->getDOMDocument();
                        $docFrag = $doc->createDocumentFragment();
                        HtmlPageCrawler::create(
                            $docFrag->appendChild($doc->createElement($this->getDOMElement()->nodeName))
                        )->append($elem);
                        foreach ($docFrag->childNodes as $child) {
                            $this->getDOMElement()->appendChild($child);
                        }
                    } else {
    					HtmlPageCrawler::create($this)->append($elem);
                    }

                    return $this->lastChild;
				}
				break;

			default:
				foreach (func_get_args() as $element) {
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
     * Prepends the element in parameter at the begining of the current Node
     * @param \DOMNode $elem
     * @return \DOMNode The node prepended
     */
    public function prepend($elem)
    {
        if ($elem instanceof DOMNode) {
            return $this->affiliate(
                $this->getDOMElement()->hasChildNodes() ?
                    $this->getDOMElement()->insertBefore($elem, $this->getDOMElement()->firstChild) :
                    $this->getDOMElement()->appendChild($elem)
            );
        } else {
            throw new \Exception('Not implemented yet.');
        }
    }

    /**
     * Affiliates an element to this object
     * @param  \DOMNode
     * @return \DOMNode
     */
    protected function affiliate($elem)
    {
        if ($elem instanceof self) {
            $elem->parentElement = $this;
        }

        return $elem;
    }

    /**
     * Empty an element <=> remove all of its children
     * @return static
     */
    public function empty()
    {
        $childNodes = $this->getDOMElement()->childNodes;

        for ($item = $childNodes->length; $item;) {
            $this->getDOMElement()->removeChild($childNodes->item(--$item));
        }

        return $this;
    }

    /**
     * (Re)Define an attribute or many attributes
     * @param string|array $attribute
     * @param string $value
     * @return static|mixed instance or the value of the attribute
     */
    public function attr($attribute, $value = null)
    {
        if(is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $this[$key] = $value;
            }
        } else {
            if (func_num_args()===1) {
                // If no value is provied, the current value is returned
                return $this[$attribute];
            } else {
                // Else the attribute value is changed (or set)
                $this[$attribute] = $value;
            }
        }
        return $this;
    }

    /**
     * (Re)Define one or several "data-" attribute(s)
     * @param string|array $attribute
     * @param string $value
     * @return static|mixed instance or the value of the dataset
     */
    public function data($attributes, $value = null)
    {
        if(is_array($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $this->data($attribute, $value);
            }
            return $this;
        } else {
            // Converts camelCase to HTML convention
            $attribute = 'data-'.ltrim(strtolower(
                preg_replace(
                    ["/([A-Z]+)/", "/-([A-Z]+)([A-Z][a-z])/"],
                    ["-$1", "-$1-$2"],
                    $attributes
                )
            ), '-');

            return func_num_args()===1 ? $this->attr($attribute) : $this->attr($attribute, $value);
        }
    }

    /**
     * Removes one or several attribute(s)
     * Alias of ->attr($attribute, null)
     * @param string ...$attribute
     * @return static instance
     */
    public function removeAttr($attribute)
    {
        array_map([$this, 'attr'], func_get_args(), array_fill(0, func_num_args(), null));
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
     * @return string|boolean The stored result in this object
     */
    public function offsetGet($attribute)
    {
        $value = $this->offsetExists($attribute) ? $this->getDOMElement()->getAttribute($attribute) : false;

        return $value===$attribute ? true : $value;
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
        if ($value===false || $value===null) {
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
     * @param string $attribute The attribute to unset
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

    /**
     * Returns the parent of the element
     * @return self|null The parent of the current object
     */
	public function parent()
	{
		return $this->parentElement;
	}

    /**
     * Removes the current object of its parent's chil nodes
     */
    public function remove()
    {
        $this->parent()->getDOMElement()->removeChild($this);
    }

    /**
     * Creates a HTML <table>
     * @param array $attr The attributes for the <table> element
     * @param int $options @see Table::__construct
     * @param int $autoRows @see Table::__construct
     * @return \aduh95\HTMLGenerator\Table The table object created
     */
    public function table($attr = array(), $options = Table::AUTO_TFOOT, $autoRows = 10)
    {
        return $this->append(new Table($this->document, $options, $autoRows))->attr($attr);
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
