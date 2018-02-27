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
    public function getFilters()
    {
        return [
            new \Twig_Filter('anselWidont', [$this, 'widontFilter']),
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
}
