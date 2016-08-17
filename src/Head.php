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
            return is_array($name) ? parent::meta($name) : ($metaElement ? $metaElement['content'] : null);
        } else {
            if (!isset($metaElement)) {
                $metaElement = parent::meta(['name'=>$name]);
            }
            $metaElement['content'] = $content;
        }

        return $this;
    }

    public function removeMeta($name)
    {
        $this->removeChild($this->getMetaElement($name));
        return $this;
    }

    protected function getMetaElement($name)
    {
        $return = $this->find('meta[@name="'.str_replace('"', '\"', $name).'"]');

        return $return->length ? HTMLElement::create($return->item(0)) : null;
    }
}
