<?php

namespace buzzingpixel\ansel\fields;

use Craft;
use craft\base\Field;

/**
 * Class Ansel
 */
class AnselField extends Field
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('ansel', 'Ansel');
    }
}
