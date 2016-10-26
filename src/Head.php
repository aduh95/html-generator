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
        if (is_array($name)) {
            // To get the default behaviour back if needed
            return parent::meta($name);
        }

        $metaElement = $this->getMetaElement($name);
        if (func_num_args() === 1) {
            return $metaElement ? $metaElement['content'] : null;
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

    /**
     * Attachs a script to the head element
     * @param array|string $src The (absolute or relative) path to the source or an array of attributs
     * @param boolean $async Sets the HTML async parameter
     * @param boolean $defer Sets the HTML defer parameter
     *
     * @return self instance
     */
    public function script($src, $async = true, $defer = true)
    {
        parent::script(is_array($src) ? $src : ['src'=>$src, 'async'=>$async, 'defer'=>$defer]);
        return $this;
    }

    /**
     * Attachs a style sheet to the head element
     * @param array|string $href The (absolute or relative) path to the source or an array of attributs
     *
     * @return self instance
     */
    public function style($href)
    {
        parent::link(is_array($href) ? $href : ['href'=>$href, 'rel'=>'stylesheet', 'type'=>"text/css"]);
        return $this;
    }
}
