<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\DOM;

use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Useful additions to the functionality of Wa72's DOM library.
 *
 * Class DOM
 * @package TB\DOM
 */
class DOM extends HtmlPageCrawler
{
    /**
     * New instance.
     *
     * @param array|\DOMNode|\DOMNodeList|string|HtmlPageCrawler $content
     * @return array|\DOMNode|\DOMNodeList|string|DOM|HtmlPageCrawler
     */
    public static function create($content)
    {
        if ($content instanceof self) {
            return $content;
        } else {
            return new self($content);
        }
    }

    /**
     * Support setting multiple attributes at once.
     *
     * @param array $attrs
     * @return $this
     */
    public function attrs(array $attrs)
    {
        foreach ($attrs as $key => $value) {
            $this->attr($key, $value);
        }
        return $this;
    }

//    /**
//     * Allow children to be an empty set and convert the result to an array.
//     *
//     * @return array
//     */
//    public function _children()
//    {
//        try {
//            $children = [];
//            foreach (parent::children() as $child) {
//                $children[] = new self($child);
//            }
//            return $children;
//        } catch (\InvalidArgumentException $e) {
//            return [];
//        }
//    }
//
//    /**
//     * Adds support for a list of nodes.
//     *
//     * @param \DOMNode|\DOMNodeList|string|HtmlPageCrawler|DOM|array $content
//     * @return $this|HtmlPageCrawler
//     */
//    public function append($content)
//    {
//        if (is_array($content)) {
//            foreach ($content as $node) {
//                parent::append($node);
//            }
//        } else {
//            parent::append($content);
//        }
//        return $this;
//    }
}