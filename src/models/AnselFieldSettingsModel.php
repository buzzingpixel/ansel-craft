<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\models;

use felicity\datamodel\Model;
use felicity\datamodel\services\datahandlers\BoolHandler;
use felicity\datamodel\services\datahandlers\IntHandler;
use felicity\datamodel\services\datahandlers\StringHandler;

/**
 * Class AnselSettingsModel
 */
class AnselFieldSettingsModel extends Model
{
    /** @var int $elementId */
    public $elementId;

    /** @var int $fieldId */
    public $fieldId;

    /** @var string $fieldName */
    public $fieldName;

    /** @var int $uploadLocation */
    public $uploadLocation;

    /** @var int $uploadFolderId */
    public $uploadFolderId;

    /** @var int $saveLocation */
    public $saveLocation;

    /** @var int $saveFolderId */
    public $saveFolderId;

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

    /** @var int $ratioWidth */
    public $ratioWidth;

    /** @var int $ratioHeight */
    public $ratioHeight;

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

    /**
     * @inheritdoc
     */
    protected function defineHandlers(): array
    {
        return [
            'elementId' => ['class' => IntHandler::class],
            'fieldId' => ['class' => IntHandler::class],
            'fieldName' => ['class' => StringHandler::class],
            'uploadLocation' => [
                'class' => IntHandler::class,
                'required' => true,
            ],
            'uploadFolderId' => ['class' => IntHandler::class],
            'saveLocation' => [
                'class' => IntHandler::class,
                'required' => true,
            ],
            'saveFolderId' => ['class' => IntHandler::class],
            'minQty' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'maxQty' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'preventUploadOverMax' => ['class' => BoolHandler::class],
            'quality' => [
                'class' => IntHandler::class,
                'min' => 1,
                'max' => 100,
                'required' => true,
            ],
            'forceJpg' => ['class' => BoolHandler::class],
            'retinaMode' => ['class' => BoolHandler::class],
            'minWidth' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'minHeight' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'maxWidth' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'maxHeight' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'ratio' => ['class' => StringHandler::class],
            'ratioWidth' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'ratioHeight' => [
                'class' => IntHandler::class,
                'min' => 0,
            ],
            'showTitle' => ['class' => BoolHandler::class],
            'requireTitle' => ['class' => BoolHandler::class],
            'titleLabel' => ['class' => StringHandler::class],
            'showCaption' => ['class' => BoolHandler::class],
            'requireCaption' => ['class' => BoolHandler::class],
            'captionLabel' => ['class' => StringHandler::class],
            'showCover' => ['class' => BoolHandler::class],
            'requireCover' => ['class' => BoolHandler::class],
            'coverLabel' => ['class' => StringHandler::class],
        ];
    }

    /**
     * Validates the ratio field
     * @param $val
     * @return array An array of errors or an empty array if no errors
     */
    public function validateRatio($val) : array
    {
        if (! $val) {
            return [];
        }

        preg_match('/^\d+(.\d+)?:\d+(.\d+)?$/', $val, $matches);

        if (\count($matches) === 1) {
            return [];
        }

        return ['Please specify crop ratio in "width:height" format using only numbers'];
    }

    /**
     * Get's the save keys
     * @return array
     */
    public function getSaveKeys() : array
    {
        return [
            'uploadLocation',
            'saveLocation',
            'minQty',
            'maxQty',
            'preventUploadOverMax',
            'quality',
            'forceJpg',
            'retinaMode',
            'minWidth',
            'minHeight',
            'maxWidth',
            'maxHeight',
            'ratio',
            'showTitle',
            'requireTitle',
            'titleLabel',
            'showCaption',
            'requireCaption',
            'captionLabel',
            'showCover',
            'requireCover',
            'coverLabel',
        ];
    }

    /**
     * Gets display types
     * @return array
     */
    public function getDisplaySettings() : array
    {
        return [
            'uploadLocation' => [
                'type' => 'volume',
                'name' => 'Upload Location',
                'instructions' => 'Where to upload source images',
                'required' => true,
            ],
            'saveLocation' => [
                'type' => 'volume',
                'name' => 'Save Location',
                'instructions' => 'Where to save captured images',
                'required' => true,
            ],
            'minQty' => [
                'type' => 'text',
                'name' => 'Min Quantity',
                'instructions' => 'Optional',
                'required' => false,
            ],
            'maxQty' => [
                'type' => 'text',
                'name' => 'Max Quantity',
                'instructions' => 'Optional',
                'required' => false,
                'defaultSettingKey' => 'defaultMaxQty',
            ],
            'preventUploadOverMax' => [
                'type' => 'lightSwitch',
                'name' => 'Prevent file uploads when max quantity reached',
                'instructions' => 'Normally, Ansel will allow uploads beyond max quantity gray them out to indicate they will not be displayed. This is great for preparing images for later. But rarely you need to keep those images from uploading at all.',
                'required' => false,
            ],
            'quality' => [
                'type' => 'text',
                'name' => 'Image Quality',
                'instructions' => 'Specify JPEG image quality (1 - 100)',
                'required' => true,
                'defaultSettingKey' => 'defaultImageQuality',
            ],
            'forceJpg' => [
                'type' => 'lightSwitch',
                'name' => 'Force JPEG',
                'instructions' => 'Force the captured image to save as JPEG',
                'required' => false,
                'defaultSettingKey' => 'defaultJpg',
            ],
            'retinaMode' => [
                'type' => 'lightSwitch',
                'name' => 'Retina Mode',
                'instructions' => 'Double dimensions for 2x output',
                'required' => false,
                'defaultSettingKey' => 'defaultRetina',
            ],
            'minWidth' => [
                'type' => 'text',
                'name' => 'Min Width',
                'instructions' => 'Optional',
                'required' => false,
            ],
            'minHeight' => [
                'type' => 'text',
                'name' => 'Min Height',
                'instructions' => 'Optional',
                'required' => false,
            ],
            'maxHeight' => [
                'type' => 'text',
                'name' => 'Max Height',
                'instructions' => 'Optional',
                'required' => false,
            ],
            'ratio' => [
                'type' => 'text',
                'name' => 'Ratio',
                'instructions' => 'Constrain image ratio if applicable (1:1, 2:1, 4:3, 16:9). Please note you should make sure your min/max width/height are not in conflict with your crop ratio.',
                'required' => false,
            ],
            'showTitle' => [
                'type' => 'lightSwitch',
                'name' => 'Display title field',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultShowTitle',
            ],
            'requireTitle' => [
                'type' => 'lightSwitch',
                'name' => 'Require title field',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultRequireTitle',
            ],
            'titleLabel' => [
                'type' => 'text',
                'name' => 'Customize title label',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultTitleLabel',
            ],
            'showCaption' => [
                'type' => 'lightSwitch',
                'name' => 'Display caption field',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultShowCaption',
            ],
            'requireCaption' => [
                'type' => 'lightSwitch',
                'name' => 'Require caption field',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultRequireCaption',
            ],
            'captionLabel' => [
                'type' => 'text',
                'name' => 'Customize caption label',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultCaptionLabel',
            ],
            'showCover' => [
                'type' => 'lightSwitch',
                'name' => 'Display cover field',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultShowCover',
            ],
            'requireCover' => [
                'type' => 'lightSwitch',
                'name' => 'Require cover field',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultRequireCover',
            ],
            'coverLabel' => [
                'type' => 'text',
                'name' => 'Customize cover label',
                'instructions' => null,
                'required' => false,
                'defaultSettingKey' => 'defaultCoverLabel',
            ],
        ];
    }

    /**
     * Gets the field settings save array
     * @return array
     * @throws \Exception
     */
    public function getSaveArray() : array
    {
        $returnVal = [];

        foreach ($this->getSaveKeys() as $prop) {
            $returnVal[$prop] = $this->getProperty($prop);
        }

        return $returnVal;
    }
}
