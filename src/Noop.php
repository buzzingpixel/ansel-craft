<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel;

/**
 * Class Noop
 */
class Noop
{
    /**
     * @param string $name
     * @param array $args
     * @return null
     */
    public function __call(string $name, array $args)
    {
        return null;
    }
}
