<?php

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
