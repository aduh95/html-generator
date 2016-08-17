<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use DOMImplementation;

/**
 * Represents a whole HTML document
 * @author aduh95
 * @api
 */
class Document
{
    /** @var \DOMImplementation The DOM implementation for this document */
    public $DOMImplementation;

    /** @var \DOMDocument The actual DOM document for this document */
    protected $dom;

    /** @var string The HTML language that will be output this document */
    protected $outputLanguage;

    /** @var string The character set of encoding for this document */
    protected $charset;

    /** @var \DOMElement The html object of the document */
    protected $html;

    /** @var Body The body object of the document */
    protected $body;

    /** @var Head The head object of the document */
    protected $head;

    /** @var \DOMDocumentFragment Some fragment to create the elements */
    protected $fragment;

    /** @var Parser The parser to parse XML into this document */
    public $parser;

    /** @var Document The last Document instance created */
    protected static $lastDocument;

    protected $css_sheets = array();
    protected $scripts = array();

    public function __construct($title = '', $lang = 'en', $language = ENT_HTML5, $charset = 'UTF-8')
    {
        self::$lastDocument = $this;

        $this->outputLanguage = $language;

        $this->DOMImplementation = new DOMImplementation;
        $this->dom = $this->DOMImplementation->createDocument();


        $this->parser = new Parser($this->dom, $charset, $language);
        $this->dom->loadXML($this->parser->getHeaders().'<html/>');

        $this->html = $this->dom->documentElement;
        $this->html->setAttribute('lang', $lang);

        $this->head = $this->html->appendChild(new Head($this));
        $this->body = $this->html->appendChild(new Body($this));

        $this->head->title()->text($title);
    }

    /**
     * Outputs the documents HTML representation aumatically at the end of the script
     */
    public function __destruct()
    {
        if (!headers_sent()) {
            echo $this;
        }
    }

    /**
     * @return Body The body element of this document
     */
    public function __invoke()
    {
        return $this->body;
    }

    /**
     * @return Body The body element of this document
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return Head The Head element of this document
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Returns a document fragment
     * @return \DOMDocumentFragment
     */
    public function getFragment()
    {
        if (!isset($this->fragment)) {
            $this->fragment = $this->getDOMDocument()->createDocumentFragment();
        }

        return $this->fragment;
    }

    /**
     * Attach CSS file or string to the document
     * @param string $css The name of the file, or the path to the file, or a direct CSS input
     * @param string $dir The directory that contains the file
     */
    public function addStyle($css, $dir=false)
    {
        $this->css_sheets[] = (empty($dir) ? null:HTML::relativeLink($dir.'/')).$css;
    }

    /**
     * Attach JavaScript file or string to the document
     * @param string $js The name of the file, or the path to the file, or a direct JavaScript input
     * @param string $dir The directory that contains the file
     * @param bool $defer If true, specifies that the script is executed when the page has finished parsing
     */
    public function addScript($js, $dir=false, $defer=true)
    {
        $url = (empty($dir) ? null:HTML::relativeLink($dir.'/')).$js_url;


        $this->scripts[] = array('url'=>$url, 'defer'=>$defer);
    }

    public function getDocumentAttachments()
    {
        $return = '';

        foreach ($this->css_sheets as $url) {
            $return.= HTML::link(['rel'=>'stylesheet', 'href'=>$url.'.css']);
        }
        foreach ($this->scripts as $url) {
            $return.= HTML::script(['src'=>$script['url'].'.js'], '');
        }

        return $return;
    }

    protected function getDoctype()
    {
        switch ($this->outputLanguage) {
            case ENT_HTML5:
                return $this->DOMImplementation->createDocumentType('html');
                break;

            case ENT_XHTML:
                return $this->DOMImplementation->createDocumentType(
                    'html',
                    '-//W3C//DTD XHTML 1.0 //EN',
                    'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'
                );

            case ENT_HTML401:
                return $this->DOMImplementation->createDocumentType(
                    'html',
                    '-//W3C//DTD HTML 4.01 //EN',
                    'http://www.w3.org/TR/html4/strict.dtd'
                );
                break;
        }
    }

    public function createElement($tagName, $rawContent = '')
    {
        return new HTMLElement($this, $tagName, $rawContent);
    }

    public function __toString()
    {
        return $this->dom->saveHTML();
    }

    public function getDOMDocument()
    {
        return $this->dom;
    }

    public static function create($DOMDocument = null)
    {
        if (is_object($DOMDocument) && $DOMDocument instanceof \DOMDocument) {
            $return = new self;
            $return->dom = $DOMDocument;
            $return->fragment = $DOMDocument->createDocumentFragment();

            return $return;
        } else {
            return self::$lastDocument ?: new self;
        }
    }
}
