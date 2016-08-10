<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use DOMImplementation;

use Wa72\HtmlPageDom\HTMLPage;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Represents a whole HTML document
 * @author aduh95
 * @api
 */
class Document extends HTMLPage
{
    /** @var \DOMImplementation The DOM implementation for this document */
    protected $DOMImplementation;

    /** @var string The HTML language that will be output this document */
    protected $outputLanguage;

    /** @var DOMElement The html object of the document */
    protected $html;

    /** @var BodyElement The body object of the document */
    protected $body;

    protected $css_sheets = array();
    protected $scripts = array();

    public function __construct($title = '', $lang = 'en', $language = ENT_HTML5)
    {
        $this->outputLanguage = $language;
        parent::__construct();

        $this->DOMImplementation = new DOMImplementation;
        $this->dom = $this->DOMImplementation->createDocument(null, 'html', $this->getDoctype());
        $this->html = $this->dom->documentElement;

        $this->body = $this->html->appendChild(new BodyElement($this));

        $this->setTitle($title);
        $this->html->setAttribute('lang', $lang);

        // $this->getDOMDocument()->formatOutput = true;
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
     * @return BodyElement The body element of this document
     */
    public function __invoke()
    {
        return $this->body;
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

    protected function getDocumentHead()
    {
        $return = '';

        if ($dom)
            $return.= HTML::meta(['charset'=>HTML::$char_encode]) .
                        HTML::meta(['http-equiv'=>'X-UA-Compatible', 'content'=>'IE=edge']) .// Interdit le mode de compatibilité sur IE
                        // HTML::meta(['http-equiv'=>'viewport', 'content'=>'width=device-width, initial-scale=1']) . // Pour les mobiles
                        HTML::meta(['name'=>'author', 'content'=>'SEIO']) .
                        HTML::title($this->title) .
                        HTML::link(['rel'=>'icon', 'href'=>HTML::relativeLink('~images/Logo_SEIO.png'), 'type'=>'image/png']);


        $return.= $this->getDocumentAttachments();

        return $return;
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

    public function createElement($tagName)
    {
        return new HTMLElement($this, $tagName);
    }

    public function __toString()
    {
        // $dom->documentElement->appendChild($this->getDocumentHead());
        // $dom->documentElement->appendChild($this->getDocumentBody());
        return $this->dom->saveHTML();
        // return .PHP_EOL.new Tag(
        //     'html',
        //     $this->attributes,
        //     .$this->getDocumentBody()
        // );
    }
}
