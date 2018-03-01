<?php

namespace buzzingpixel\ansel\twigextensions;

use craft\helpers\Template;

/**
 * Class AnselTwigExtension
 */
class AnselTwigExtension extends \Twig_Extension
{
    /**
     * Returns the twig filters
     * @return \Twig_Filter[]
     */
    public function getFilters() : array
    {
        return [
            new \Twig_Filter('anselWidont', [$this, 'widontFilter']),
            new \Twig_Filter('anselMinify', [$this, 'minify']),
        ];
    }

    /**
     * Returns twig functions
     * @return \Twig_Function[]
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_Function('anselUniqueId', [$this, 'uniqueId']),
        ];
    }

    /**
     * Widont filter
     * @param string $str
     * @return \Twig_Markup
     */
    public function widontFilter(string $str) : \Twig_Markup
    {
        // This regex is a beast, tread lightly
        $widontTest = "/([^\s])\s+(((<(a|span|i|b|em|strong|acronym|caps|sub|sup|abbr|big|small|code|cite|tt)[^>]*>)*\s*[^\s<>]+)(<\/(a|span|i|b|em|strong|acronym|caps|sub|sup|abbr|big|small|code|cite|tt)>)*[^\s<>]*\s*(<\/(p|h[1-6]|li)>|$))/i";

        return Template::raw(preg_replace($widontTest, '$1&nbsp;$2', $str));
    }

    /**
     * Minify filter
     * @param string $str
     * @return \Twig_Markup
     */
    public function minify(string $str) : \Twig_Markup
    {
        return Template::raw(\Minify_HTML::minify($str));
    }

    /**
     * UniqueID function
     * @return \Twig_Markup
     */
    public function uniqueId() : \Twig_Markup
    {
        return Template::raw(uniqid('', false));
    }
}
