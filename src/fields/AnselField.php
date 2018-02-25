<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\fields;

use buzzingpixel\ansel\models\AnselFieldSettingsModel;
use Craft;
use craft\base\Field;
use craft\volumes\Local as LocalVolumeType;
use buzzingpixel\ansel\Ansel;

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
        $volumeSelectOptions = [[
            'label' => Craft::t('app', 'Choose Location...'),
            'name' => '',
            'value' => '',
        ]];

        foreach (Craft::$app->getVolumes()->getAllVolumes() as $volume) {
            /** @var LocalVolumeType $volume */
            $volumeSelectOptions[] = [
                'label' => $volume->name,
                'name' => $volume->id,
                'value' => $volume->id,
            ];
        }

        return Craft::$app->getView()->renderTemplate(
            'ansel/_core/FieldSettings.twig',
            [
                'settings' => Ansel::$plugin->getAnselSettingsService()->getSettings(),
                'volumeSelectOptions' => $volumeSelectOptions,
                'fieldSettingsModel' => new AnselFieldSettingsModel(),
            ]
        );
    }
}
