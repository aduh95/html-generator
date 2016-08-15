<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

use DOMDocument;

/**
 * Parse XHTML string to DOMElements
 * @author aduh95
 */
class Parser
{
    /** @var string Character set of encoding */
    public $charset;

    /** @var array The entities of this document */
    public $entities;

    /** @var \DOMDocument The document to perform the parsing */
    protected $document;

    public function __construct($document, $charset, $outputLanguage)
    {
        $this->document = $document;
        $this->charset = $charset;
        $this->outputLanguage = $outputLanguage;
    }

    /**
     * @param mixed $content The content to parse
     * @return \DOMNode
     */
    public function parse($content)
    {
        if (is_string($content)) {
            return $this->parseXML($content);
        } elseif (!is_object($content)) {
            return $this->document->createTextNode(strval($content));
        } elseif ($content instanceof DOMElement) {
            return $content;
        } elseif ($content instanceof DOMNodeList) {
            $fragment = $document->createDocumentFragment();

            foreach ($content as $child) {
                $fragment->appendChild($document->importNode($child, true));
            }

            return $fragment;
        } else {
            return new EmptyElement;
        }
    }

    /**
     * Returns the child nodes equivalent to the XHTML string passed as parameter
     * @param string $xhtml The xHTML string (has to be valid XML)
     * @return \DOMDocumentFragment
     */
    public function parseXML($value = '')
    {
        $fragment = $this->document->createDocumentFragment();
        $fragment->appendXML($value);

        return $fragment;
    }

    protected static function getDocument()
    {
        if (!isset(self::$document)) {
            self::$document = new DOMDocument;
            self::$document->loadXML(self::getHeaders().'<xml></xml>');
        }

        return self::$document;
    }

    public function getHeaders()
    {
        return '<?xml version="1.0" encoding="'.$this->charset.'"?>'.PHP_EOL.$this->getDoctype().PHP_EOL;
    }

    protected function getDoctype()
    {
        $entities = array();
        foreach ($this->getEntities() as $entity => $value) {
            $entities[]= '<!ENTITY '.$entity.' "'.
                htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, $this->charset).
                '">';
        }

        $elem = $this->getDoctypeElements();

        return '<!DOCTYPE '.
            $elem[0].
            (empty($elem[1]) ? null : ' PUBLIC "'.$elem[1].'"').
            (empty($elem[2]) ? null : ' "'.$elem[2].'"').
            '['.implode('', $entities).']>';
    }


    protected function getDoctypeElements()
    {
        switch ($this->outputLanguage) {
            case ENT_HTML5:
                return ['html'];
                break;

            case ENT_XHTML:
                return array(
                    'html',
                    '-//W3C//DTD XHTML 1.0 //EN',
                    'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'
                );

            case ENT_HTML401:
                return array(
                    'html',
                    '-//W3C//DTD HTML 4.01 //EN',
                    'http://www.w3.org/TR/html4/strict.dtd'
                );
                break;
        }
    }


    protected function getEntities()
    {
        if(!isset($this->entities)) {
            $this->entities = array();
            foreach (array_diff(
                get_html_translation_table(HTML_ENTITIES, $this->outputLanguage | ENT_NOQUOTES, $this->charset),
                get_html_translation_table(HTML_ENTITIES, ENT_XML1 | ENT_NOQUOTES, $this->charset),
                ['&percnt;']
            ) as $value => $entity) {
                 $this->entities[substr($entity, 1, -1)] = $value;
            }
        }

        return $this->entities;
    }
}