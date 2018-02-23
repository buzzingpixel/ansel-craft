<?php

namespace buzzingpixel\ansel;

use craft\base\Plugin;

/**
 * Class Ansel
 */
class Ansel extends Plugin
{
    /** @var Ansel $plugin */
    public static $plugin;

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Make sure parent init functionality runs
        parent::init();

        // Save an instance of this plugin for easy reference throughout app
        self::$plugin = $this;
    }
}
