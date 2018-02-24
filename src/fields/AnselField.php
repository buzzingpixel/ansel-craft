<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\fields;

use buzzingpixel\ansel\Ansel;
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

    /**
     * Gets the Ansel field type settings HTML
     * @return string
     * @throws \Exception
     */
    public function getSettingsHtml() : string
    {
        var_dump(Craft::$app->getVolumes()->getAllVolumes());
        return Craft::$app->getView()->renderTemplate(
            'ansel/_core/FieldSettings.twig',
            [
                'settings' => Ansel::$plugin->getAnselSettingsService()->getSettings(),
            ]
        );
    }
}
