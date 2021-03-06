<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\models;

use Craft;
use craft\models\VolumeFolder;
use craft\volumes\Local;
use felicity\datamodel\Model;
use felicity\datamodel\services\datahandlers\IntHandler;
use felicity\datamodel\services\datahandlers\BoolHandler;
use felicity\datamodel\services\datahandlers\StringHandler;

/**
 * Class AnselSettingsModel
 */
class AnselFieldSettingsModel extends Model
{
    /** @var bool $isRetinized */
    private $isRetinized = false;

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

    /** @var int $highQualFolderId */
    public $highQualFolderId;

    /** @var int $thumbFolderId */
    public $thumbFolderId;

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
            'highQualFolderId' => ['class' => IntHandler::class],
            'thumbFolderId' => ['class' => IntHandler::class],
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
     * Sets ratio width and height
     * @param $val
     * @return string
     * @throws \ReflectionException
     */
    public function castRatio($val) : string
    {
        if (! $val) {
            return (string) $val;
        }

        $parts = explode(':', $val);

        if (! isset($parts[0])) {
            return $val;
        }

        $this->setProperty('ratioWidth', $parts[0]);

        if (! isset($parts[1])) {
            return (string) $val;
        }

        $this->setProperty('ratioHeight', $parts[1]);

        return (string) $val;
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
            'maxWidth' => [
                'type' => 'text',
                'name' => 'Max Width',
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

    /**
     * Retinize values if applicable
     * @throws \Exception
     */
    public function retinizeValues()
    {
        if ($this->isRetinized || ! $this->getProperty('retinaMode')) {
            return;
        }

        $this->setProperty('minWidth', $this->getProperty('minWidth') * 2);
        $this->setProperty('minHeight', $this->getProperty('minHeight') * 2);
        $this->setProperty('maxWidth', $this->getProperty('maxWidth') * 2);
        $this->setProperty('maxHeight', $this->getProperty('maxHeight') * 2);

        $this->isRetinized = true;
    }

    /**
     * Deretinize values
     * @throws \ReflectionException
     */
    public function deRetinizeValues()
    {
        if (! $this->isRetinized || ! $this->getProperty('retinaMode')) {
            return;
        }

        $this->setProperty('minWidth', $this->getProperty('minWidth') / 2);
        $this->setProperty('minHeight', $this->getProperty('minHeight') / 2);
        $this->setProperty('maxWidth', $this->getProperty('maxWidth') / 2);
        $this->setProperty('maxHeight', $this->getProperty('maxHeight') / 2);

        $this->isRetinized = false;
    }

    /**
     * Checks if we need to show modal fileds
     * @return bool
     */
    public function hasModalFields() : bool
    {
        return $this->showTitle || $this->showCaption || $this->showCover;
    }

    /**
     * @param $val
     */
    public function setUploadLocationFromUid($val)
    {
        if (! $val) {
            return;
        }

        $this->uploadLocationUid = $val;

        $volume = Craft::$app->getVolumes()->getVolumeByUid($val);

        $this->uploadLocation = (int) $volume->id;
    }

    private $uploadLocationUid;

    /**
     * @return string|null
     */
    public function getUploadLocationUid()
    {
        if ($this->uploadLocationUid) {
            return $this->uploadLocationUid;
        }

        if (! $this->uploadLocation) {
            return null;
        }

        $volume = Craft::$app->getVolumes()->getVolumeById($this->uploadLocation);

        if (! $volume) {
            return null;
        }

        $this->uploadLocation = $volume->uid;

        return $this->uploadLocation;
    }

    /**
     * @param $val
     */
    public function setSaveLocationFromUid($val)
    {
        if (! $val) {
            return;
        }

        $this->saveLocationUid = $val;

        $volume = Craft::$app->getVolumes()->getVolumeByUid($val);

        if (! $volume) {
            return;
        }

        $this->saveLocation = (int) $volume->id;
    }

    private $saveLocationUid;

    /**
     * @return string|null
     */
    public function getSaveLocationUid()
    {
        if ($this->saveLocationUid) {
            return $this->saveLocationUid;
        }

        if (! $this->saveLocation) {
            return null;
        }

        $volume = Craft::$app->getVolumes()->getVolumeById($this->saveLocation);

        if (! $volume) {
            return null;
        }

        $this->saveLocationUid = $volume->uid;

        return $this->saveLocationUid;
    }

    /**
     * Gets upload folder ID
     * @param $val
     * @return int
     */
    public function castUploadFolderId($val) : int
    {
        if ($val !== null) {
            return $val;
        }

        $volume = Craft::$app->getVolumes()->getVolumeById(
            $this->uploadLocation
        );

        if (! $volume) {
            $this->uploadFolderId = 0;
            return 0;
        }

        /** @var Local $volume */

        $folder = Craft::$app->getAssets()->findFolder([
            'name' => $volume->name,
            'volumeId' => $volume->id,
        ]);

        if (! $folder) {
            $this->uploadFolderId = 0;
            return 0;
        }

        $this->uploadFolderId = (int) $folder->id;
        $this->uploadFolderUid = $folder->uid;

        return $this->uploadFolderId;
    }

    private $uploadFolderUid;

    /**
     * Gets the upload folder UID
     * @return mixed
     * @throws \ReflectionException
     */
    public function getUploadFolderUid()
    {
        $this->getProperty('uploadFolderId');
        return $this->uploadFolderUid;
    }

    /**
     * Gets save folder ID
     * @param $val
     * @return int
     */
    public function castSaveFolderId($val) : int
    {
        if ($val !== null) {
            return $val;
        }

        $volume = Craft::$app->getVolumes()->getVolumeById(
            $this->saveLocation
        );

        if (! $volume) {
            $this->saveFolderId = 0;
            return 0;
        }

        /** @var Local $volume */

        $folder = Craft::$app->getAssets()->findFolder([
            'name' => $volume->name,
            'volumeId' => $volume->id,
        ]);

        if (! $folder) {
            $this->saveFolderId = 0;
            return 0;
        }

        $this->saveFolderId = (int) $folder->id;
        $this->saveFolderUid = $folder->uid;

        return $this->saveFolderId;
    }

    private $saveFolderUid;

    /**
     * Ges the save folder UID
     * @return mixed
     * @throws \ReflectionException
     */
    public function getSaveFolderUid()
    {
        $this->getProperty('saveFolderId');
        return $this->saveFolderUid;
    }

    /**
     * Gets high quality folder ID
     * @param $val
     * @return int
     * @throws \Exception
     */
    public function castHighQualFolderId($val) : int
    {
        if ($val !== null) {
            return $val;
        }

        $volume = Craft::$app->getVolumes()->getVolumeById(
            $this->saveLocation
        );

        if (! $volume) {
            $this->highQualFolderId = 0;
            return 0;
        }

        /** @var Local $volume */

        $folder = Craft::$app->getAssets()->findFolder([
            'name' => 'ansel_high_qual',
            'volumeId' => $volume->id,
        ]);

        if (! $folder) {
            $parentFolder = Craft::$app->getAssets()->findFolder([
                'name' => $volume->name,
                'volumeId' => $volume->id,
            ]);

            if ($parentFolder === null) {
                throw new \Exception('Parent folder not found');
            }

            /** @var VolumeFolder $parentFolder */

            $folder = new VolumeFolder();

            $folder->parentId = $parentFolder->id;
            $folder->volumeId = $volume->id;
            $folder->name = 'ansel_high_qual';
            $folder->path = 'ansel_high_qual/';

            Craft::$app->getAssets()->createFolder($folder);
        }

        $this->highQualFolderId = $folder->id;

        return $this->highQualFolderId;
    }

    /**
     * Gets thumb folder ID
     * @param $val
     * @return int
     * @throws \Exception
     */
    public function castThumbFolderId($val) : int
    {
        if ($val !== null) {
            return $val;
        }

        $volume = Craft::$app->getVolumes()->getVolumeById(
            $this->saveLocation
        );

        if (! $volume) {
            $this->thumbFolderId = 0;
            return 0;
        }

        /** @var Local $volume */

        $folder = Craft::$app->getAssets()->findFolder([
            'name' => 'ansel_thumbs',
            'volumeId' => $volume->id,
        ]);

        if (! $folder) {
            $parentFolder = Craft::$app->getAssets()->findFolder([
                'name' => $volume->name,
                'volumeId' => $volume->id,
            ]);

            if ($parentFolder === null) {
                throw new \Exception('Parent folder not found');
            }

            /** @var VolumeFolder $parentFolder */

            $folder = new VolumeFolder();

            $folder->parentId = $parentFolder->id;
            $folder->volumeId = $volume->id;
            $folder->name = 'ansel_thumbs';
            $folder->path = 'ansel_thumbs/';

            Craft::$app->getAssets()->createFolder($folder);
        }

        $this->thumbFolderId = $folder->id;

        return $this->thumbFolderId;
    }

    /**
     * @param bool $excludeUuid
     * @return array
     * @throws \ReflectionException
     */
    public function asArray(bool $excludeUuid = false): array
    {
        $array = parent::asArray($excludeUuid);

        $array['uploadLocationUid'] = $this->getUploadLocationUid();
        $array['saveLocationUid'] = $this->getSaveLocationUid();
        $array['uploadFolderUid'] = $this->getUploadFolderUid();
        $array['saveFolderUid'] = $this->getSaveFolderUid();

        return $array;
    }
}
