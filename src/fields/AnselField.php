<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\fields;

use buzzingpixel\ansel\models\AnselSettingsModel;
use Craft;
use yii\db\Schema;
use craft\base\Field;
use buzzingpixel\ansel\Ansel;
use craft\volumes\Local as LocalVolumeType;
use buzzingpixel\ansel\models\AnselFieldSettingsModel;

/**
 * Class Ansel
 */
class AnselField extends Field
{
    /** @var int $uploadLocation */
    public $uploadLocation;

    /** @var int $saveLocation */
    public $saveLocation;

    /** @var int $minQty */
    public $minQty;

    /** @var int $maxQty */
    public $maxQty;

    /** @var bool $preventUploadOverMax */
    public $preventUploadOverMax;

    /** @var int $quality */
    public $quality;

    /** @var bool $forceJpg */
    public $forceJpg;

    /** @var bool $retinaMode */
    public $retinaMode;

    /** @var int $minWidth */
    public $minWidth;

    /** @var int $minHeight */
    public $minHeight;

    /** @var int $maxWidth */
    public $maxWidth;

    /** @var int $maxHeight */
    public $maxHeight;

    /** @var string $ratio */
    public $ratio;

    /** @var bool $showTitle */
    public $showTitle;

    /** @var bool $requireTitle */
    public $requireTitle;

    /** @var bool $titleLabel */
    public $titleLabel;

    /** @var bool $showCaption */
    public $showCaption;

    /** @var bool $requireCaption */
    public $requireCaption;

    /** @var string $captionLabel */
    public $captionLabel;

    /** @var bool $showCover */
    public $showCover;

    /** @var bool $requireCover */
    public $requireCover;

    /** @var string $coverLabel */
    public $coverLabel;

    /** @var AnselSettingsModel $settingsModel */
    private $fieldSettingsModel;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('ansel', 'Ansel');
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Get's the settings model
     * @throws \Exception
     */
    public function getSettingsModel()
    {
        if (! $this->fieldSettingsModel) {
            $this->fieldSettingsModel = new AnselFieldSettingsModel();
            $this->fieldSettingsModel->setProperty('fieldId', $this->id);

            foreach ($this->getSettings() as $key => $val) {
                if (! $val) {
                    continue;
                }
                $this->fieldSettingsModel->setProperty($key, $val);
            }
        }

        return $this->fieldSettingsModel;
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
                'fieldSettingsModel' => $this->getSettingsModel(),
            ]
        );
    }

    /**
     * Gets settings
     * @param bool $isNew
     * @return bool
     * @throws \Exception
     */
    public function beforeSave(bool $isNew): bool
    {
        parent::beforeSave($isNew);
        return $this->getSettingsModel()->validate();
    }
}
