<?php
/**
 * @package aduh95/HTMLGenerator
 * @license MIT
 */

namespace aduh95\HTMLGenerator;

/**
 * Represents a HTML head node
 * @author aduh95
 * @api
 */
class Head extends SubRootElement
{
    public function __construct(Document $dom)
    {
        parent::__construct($dom, 'head');
    }

    /**
     * Sets or returns the content of a <meta> identified by name
     * @param string $name The name of the <meta>
     * @param mixed $content The content of the <meta>
     * @return self instance
     */
    public function meta($name, $content = null)
    {
        $metaElement = $this->getMetaElement($name);
        if (func_num_args() === 1) {
            return $metaElement['content'] ?: null;
        } else {
            $metaElement['content'] = $content;
        }

        return $this;
    }

    protected function getMetaElement($name)
    {
        foreach ($this->childNodes as $child) {
            $childElement = HTMLElement::create($child);
            if($childElement->is('meta') && $childElement['name'] === $name) {
                return $childElement;
            }
        }

        return parent::meta()->attr('name', $name);
    }
}
