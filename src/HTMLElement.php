<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use ArrayAccess;
use DOMNode;
use DOMElement;

/**
 * Represents any HTML element
 * @author aduh95
 * @api
 */
class HTMLElement extends DOMElement implements ArrayAccess
{
    /** @var Document The owner document of this element */
	protected $document;

    /** @var \DOMNode The parent node of this element */
	protected $parentElement;

    /**
     * Replaces the elements by actual HTMLElement
     * @param \DOMElement|self $element
     * @return self|EmptyElement An instance of the converted element
     */
    public static function create($element)
    {
        if ($element instanceof self) {
            return $element;
        } elseif ($element instanceof DOMElement) {
            $document = Document::create($element->ownerDocument);

            $attr = array();
            if ($element->hasAttributes()) {
                foreach ($element->attributes as $attribute) {
                    $attr[$attribute->name] = $attribute->value;
                }
            }
            $content = array();
            if ($element->hasChildNodes()) {
                foreach ($element->childNodes as $child) {
                    $content[] = $child->cloneNode(true);
                }
            }

            return (new self(
                $document,
                $element->nodeName,
                $element->nodeValue
            ))->attr($attr)->append($content)->replace($element);

        } else {
            return new EmptyElement;
        }

    }

    /**
     * Object constructor
     * @param \aduh95\HTMLGenerator\Document $dom The Document object which owns this element
     * @param string $tagName The tag name of this element
     * @param string $rawContent The raw content of this element. If it is not XML valid, it will raise an error
     * @throws \DOMException If the raw content is not valid
     */
	public function __construct(Document $dom, $tagName, $rawContent = '')
	{
        $deepRawContent = !empty($rawContent) && strpos($rawContent, '<')!==false;

		parent::__construct($tagName);

		$this->document = $dom;
        $dom->getFragment()->appendChild($this);

        if ($deepRawContent) {
            $this->append($rawContent);
        } elseif (!empty($rawContent)) {
            $this->nodeValue = $rawContent;
        }
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

		$this->append($this->getDOMDocument()->createTextNode($text));
		return $this;
	}

    /**
     * Appends the element(s) in parameter at the end of the current Node,
     * or returns an EmptyElement which has this element as parent.
     * @param mixed ...$elem
     * @return self | EmptyElement
     */
	public function append($elem = null)
	{
		switch (func_num_args()) {
			case 0:
                $return = new EmptyElement;
				$this->append($return);
                return $return;
				break;

			case 1:
                if (is_array($elem)) {
                    array_map([$this, __METHOD__], $elem);
                } elseif (is_object($elem) && $elem instanceof DOMNode) {

                    $return = $elem instanceof EmptyElement ?
                        $elem :
                        $this->getDOMElement()->appendChild(
                            $elem->ownerDocument===$this->ownerDocument ?
                                $elem :
                                $this->ownerDocument->importNode($elem, true)
                        );
                    $this->affiliate($return);
				} else {
                    if (!empty($elem)) {
                        $this->getDOMElement()->appendChild($this->document->parser->parse($elem));
                    }
				}
				break;

			default:
                return call_user_func([$this, __METHOD__], func_get_args());
				break;
		}
        return $this;
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
        if ($elem instanceof self || $elem instanceof EmptyElement) {
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

    /**
     * Appends an HTMLElement at the end of this document
     * @param string $tagName The tag name of the new element
     * @param mixed[] $content The attributes and the raw XML content
     * @return self The new element
     */
    public function __call($tagName, $content)
    {
        $return = new self($this->document, $tagName);
        $return
            ->attr(count($content) && is_array($content[0]) ? array_shift($content) : array())
            ->append($content);
        $this->append($return);
        return $return;
    }

    /**
     * @return self Alias for parent
     */
    public function __invoke()
    {
    	return $this->parent();
    }

    /**
     * @return string The HTML value of the element
     */
	public function __toString()
	{
        $document = new \DOMDocument;
        $document->appendChild($document->importNode($this->getDOMElement(), true));
		return $document->saveHTML();
	}

    /**
     * Returns the parent of the element
     * @return self|null The parent of the current object
     */
	public function parent()
	{
		return $this->parentElement ?: $this->parentNode;
	}

    /**
     * Finds an element in the children and descendents
     * @param string $xPathQuery @see \DOMXPath::query
     * @return \DOMNodeList The set of result(s)
     */
    public function find($xPathQuery)
    {
        return $this->document->parser->xPath($xPathQuery, $this);
    }

    /**
     * Removes the current object of its parent's chil nodes
     */
    public function remove()
    {
        $this->parent()->removeChild($this);
    }

    /**
     * Tells if the node name of this element is
     * @param string $nodeName The node name to test
     * @return boolean
     */
    public function is($nodeName)
    {
        return !strcasecmp($this->getDOMElement()->nodeName, $nodeName);
    }

    /**
     * Replaces the node in argument by this element if possible
     * @param \DOMNode $element The node to be replaced
     * @return self instance
     */
    public function replace($element)
    {
        if ($element instanceof DOMNode && isset($element->parentNode)) {
            $element->parentNode->replaceChild(
                $this,
                $element
            );
        }

        return $this;
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
        $table = new Table($this->document, $options, $autoRows);
        $this->append($table->attr($attr));
        return $table;
    }

    /**
     * Creates a HTML <ul>
     * This method has the following signature:
     * List HTMLElement::ul([array $attributes,][array $listItems])
     * @param array $attributes The attributes for the <ul> element
     * @param string[]|HTMLElement[]|\DOMNodeList $listItems The items of the list
     * @return \aduh95\HTMLGenerator\List The list object created
     */
    public function ul($attributes = array(), $listItems = array())
    {
        return $this->appendList('ul', $attributes, $listItems);
    }

    /**
     * Creates a HTML <ol>
     * This method has the following signature:
     * List HTMLElement::ol([array $attributes,][array $listItems])
     * @param array $attributes The attributes for the <ol> element
     * @param string[]|HTMLElement[]|\DOMNodeList $listItems The items of the list
     * @return \aduh95\HTMLGenerator\List The list object created
     */
    public function ol($attributes = array(), $listItems = array())
    {
        return $this->appendList('ol', $attributes, $listItems);
    }

    /**
     * Creates a HTML list
     * @param string $tagName
     * @param array $attributes The attributes for the <ol> element
     * @param string[]|HTMLElement[]|\DOMNodeList $listItems The items of the list
     * @return \aduh95\HTMLGenerator\List The list object created
     */
    protected function appendList($tagName, $attributes = array(), $listItems = array())
    {
        if (is_object($attributes) || (count($attributes) && isset($attributes[0]))) {
            $listItems = $attributes;
            $attributes = array();
        }
        $list = new HTMLList($this->document, $tagName);
        $this->append($list->attr($attributes)->append($listItems));
        return $list;
    }

    /**
     * Creates a video element. This method could be used to shortcut the HTML generation
     * @param array $attr Attributes of the <video> element. You can also specify "src" as
     *                      an array, it will create <source> child elements.
     * @param null|string $fallbackTxt The message which will be displayed for browsers that do
     *                              not support the <video> element
     * @return self The <video> element that has been created
     */
    public function video($attr = array(), $fallbackTxt = null)
    {
        $return = new self($this->document, 'video');

        if (isset($attr['src']) && is_array($attr['src'])) {
            foreach ($attr['src'] as $type => $src) {
                $return->source([
                    'type'=>is_numeric($type) ? null : $type,
                    'src'=>$src,
                ]);
            }
            unset($attr['src']);
        }

        $return->text($fallbackTxt);
        $this->append($return);

        return $return->attr($attr);
    }

    /**
     * Creates a <form> element
     * @param array $attr Attributes of the element
     * @return Form
     */
    public function form($attr = array())
    {
        $return = new Form($this->document, $attr);
        $this->append($return);

        return $return;
    }

    /**
     * Creates a document fragment that contains a new {input, select, textarea} element.
     * @param array $attr The attributes of the input.
     *      You can also pass keys that won't be interpreted as attributes of the element:
     *       - label: string (a <label> element will be created)
     *       - help: string (a <div class="help-block"> element will be created)
     *       - list: string [] (a <datalist> will be created)
     *       - options: string [] (<option> elements will be created)
     *      Please note that if you try to modify a non attribute key as ArrayAccess key, it will lead to unexpected results.
     *      @exemple $input = $elem->input(['label'=>'Your name:']); // GOOD! This will output: <label>Your name:<input></label>
     *      @exemple $input = $elem->input();$input['label'] = 'Your name:'; // WRONG! This will output: <input label="Your name;">
     * @return self The input element generated
     */
    public function input($attr = array(), $defaultValues = array())
    {
        $return = $this->div(['class'=>'form-group']);

        if (empty($attr['type'])) {
            $attr['type'] = 'text';
        }
        $type = $attr['type'];


        // Set the default value if exists
        if (!array_key_exists('value', $attr) && isset($attr['name']) && isset($defaultValues[$attr['name']])) {
            $attr['value'] = $defaultValues[$attr['name']];
        }


        $attr['class'] = isset($attr['class']) ? $attr['class'] : 'form-control';
        $inputEmbeded = $type==='radio' || $type==='checkbox';

        if ($type==='textarea') {
            $input = new self($this->document, 'textarea');
            $input->text($attr['value']);
            unset($attr['value']);
        } elseif ($type==='select' || $type==='datalist') {
            $input = new self($this->document, $type);
        } else {
            $input = new self($this->document, 'input');
        }



        if (isset($attr['label'])) {
            $label = $return->label();

            if ($inputEmbeded) {
                $label->append($input);
            } else {
                $return->append($input);
                if (empty($attr['id'])) {
                    $attr['id'] = $this->document->generateID($input);
                }
                $label['for'] = $attr['id'];
            }

            $label->text($attr['label']);
            unset($attr['label']);
        } else {
            $return->append($input);
        }

        if (isset($attr['options'])) {
            $val = empty($attr['value']) ? array() : (array)$attr['value'];

            foreach ($attr['options'] as $key => $value)
            {
                // Support for <optgroup> elements
                if (is_array($value)) {
                    $optgroup = $input->optgroup(['label'=>$key]);
                    foreach ($value as $subkey => $option) {
                        $optgroup->option(['selected'=>in_array($subkey, $val), 'value'=>$subkey])->text($option);
                    }
                } else {
                    $input->option(['selected'=>in_array($key, $val), 'value'=>$key])->text($value);
                }
            }

            unset($attr['options']);
        }

        // Support for <datalist> elements
        if (isset($attr['list']) && is_array($attr['list'])) {
            $datalist = $return->datalist();

            foreach ($attr['list'] as $key => $value) {
                $datalist->option()->text($value)->attr('value', is_numeric($key) ? null : $key);
            }


            $attr['list'] = $this->document->generateID($datalist);
        }

        // Support for Twitter's bootstrap help block
        if (isset($attr['help'])) {
            $return->div(['class'=>'help-block'])->text($attr['help']);
            unset($attr['help']);
        }

        $input->attr($attr);


        // On retourne le code HTML généré
        return $return;
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
