<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\variables;

use buzzingpixel\ansel\Ansel;
use buzzingpixel\ansel\services\AnselImageService;

/**
 * Class AnselImageService
 */
class AnselVariable
{
    /**
     * Provides access to the AnselImageService in Twig
     * @return AnselImageService
     * @throws \Exception
     */
    public static function images() : AnselImageService
    {
        return Ansel::$plugin->getAnselImageService();
    }
}
