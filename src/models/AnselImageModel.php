<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\models;

use Craft;
use craft\elements\User;
use craft\elements\Asset;
use buzzingpixel\ansel\Ansel;
use felicity\datamodel\Model;
use felicity\datamodel\services\datahandlers\IntHandler;
use felicity\datamodel\services\datahandlers\BoolHandler;
use buzzingpixel\ansel\services\AnselLivePreviewMockAsset;
use felicity\datamodel\services\datahandlers\StringHandler;
use felicity\datamodel\services\datahandlers\DateTimeHandler;

/**
 * Class AnselImageModel
 */
class AnselImageModel extends Model
{
    /** @var bool $isLivePreview */
    public $isLivePreview;

    /** @var int $id */
    public $id;

    /** @var int $elementId */
    public $elementId;

    /** @var int $fieldId */
    public $fieldId;

    /** @var int $userId */
    public $userId;

    /** @var int $assetId */
    public $assetId;

    /** @var int $highQualAssetId */
    public $highQualAssetId;

    /** @var int $thumbAssetId */
    public $thumbAssetId;

    /** @var int $originalAssetId */
    public $originalAssetId;

    /** @var int $width */
    public $width;

    /** @var int $height */
    public $height;

    /** @var int $x */
    public $x;

    /** @var int $y */
    public $y;

    /** @var string $title */
    public $title;

    /** @var string $caption */
    public $caption;

    /** @var bool $cover */
    public $cover;

    /** @var int $position */
    public $position;

    /** @var bool $disabled */
    public $disabled;

    /** @var \DateTime $dateCreated */
    public $dateCreated;

    /** @var \DateTime $dateUpdated */
    public $dateUpdated;

    /** @var string $uid */
    public $uid;

    /**
     * Following properties are used by the field type for postback
     */
    public $delete;
    public $cacheFile;
    public $fileName;
    public $preManipulation = [];

    /**
     * @inheritdoc
     */
    protected function defineHandlers(): array
    {
        return [
            'isLivePreview' => ['class' => BoolHandler::class],
            'id' => ['class' => IntHandler::class],
            'elementId' => ['class' => IntHandler::class],
            'fieldId' => ['class' => IntHandler::class],
            'userId' => ['class' => IntHandler::class],
            'assetId' => ['class' => IntHandler::class],
            'highQualAssetId' => ['class' => IntHandler::class],
            'thumbAssetId' => ['class' => IntHandler::class],
            'originalAssetId' => ['class' => IntHandler::class],
            'width' => ['class' => IntHandler::class],
            'height' => ['class' => IntHandler::class],
            'x' => ['class' => IntHandler::class],
            'y' => ['class' => IntHandler::class],
            'title' => ['class' => StringHandler::class],
            'caption' => ['class' => StringHandler::class],
            'cover' => ['class' => BoolHandler::class],
            'position' => ['class' => IntHandler::class],
            'disabled' => ['class' => BoolHandler::class],
            'dateCreated' => ['class' => DateTimeHandler::class],
            'dateUpdated' => ['class' => DateTimeHandler::class],
            'uid' => ['class' => StringHandler::class],
        ];
    }

    /**
     * Gets the user
     * @return null|User
     */
    public function getUser()
    {
        return self::getUserFromId($this->userId);
    }

    /**
     * Gets asset
     * @return null|Asset
     * @throws \Exception
     */
    public function getAsset()
    {
        if ($this->isLivePreview &&
            $this->getFromPreManipulationArray('standardImgCacheLocation')
        ) {
            $mockAsset = new AnselLivePreviewMockAsset();
            $mockAsset->filename = 'Live Preview';
            $mockAsset->kind = 'image';

            $cacheService = Ansel::$plugin->getFileCacheService();
            $cachePath = $cacheService->getCachePath();
            $mimeType = (new \finfo())->file(
                "{$cachePath}/{$this->getFromPreManipulationArray('standardImgCacheLocation')}",
                FILEINFO_MIME_TYPE
            );
            $base64 = "data:image/{$mimeType};base64,";
            $base64 .= base64_encode($cacheService->getCacheFileContents(
                $this->getFromPreManipulationArray('standardImgCacheLocation')
            ));

            $mockAsset->base64Image = $base64;

            return $mockAsset;
        }

        if (! $this->assetId) {
            return null;
        }

        return self::getAssetFromId($this->assetId);
    }

    /**
     * Gets high quality asset
     * @return null|Asset
     */
    public function getHighQualAsset()
    {
        if (! $this->highQualAssetId) {
            return null;
        }

        return self::getAssetFromId($this->highQualAssetId);
    }

    /**
     * Gets thumb quality asset
     * @return null|Asset
     */
    public function getThumbAsset()
    {
        if (! $this->thumbAssetId) {
            return null;
        }

        return self::getAssetFromId($this->thumbAssetId);
    }

    /**
     * Gets original asset
     * @return null|Asset
     */
    public function getOriginalAsset()
    {
        if (! $this->originalAssetId) {
            return null;
        }

        return self::getAssetFromId($this->originalAssetId);
    }

    /**
     * Gets an asset from the asset id
     * @param int $assetId
     * @return null|Asset
     */
    private static function getAssetFromId(int $assetId)
    {
        $assets = Ansel::$plugin->getStorageService()->get('assets') ?: [];

        if (isset($assets[$assetId])) {
            return $assets[$assetId];
        }

        $asset = Asset::find()->id($assetId)->one();

        if (! $asset) {
            return null;
        }

        $assets[$assetId] = $asset;

        Ansel::$plugin->getStorageService()->set($assets, 'assets');

        return $asset;
    }

    /**
     * @param int $userId
     * @return null|User
     */
    private static function getUserFromId(int $userId)
    {
        $users = Ansel::$plugin->getStorageService()->get('users') ?: [];

        if (isset($users[$userId])) {
            return $users[$userId];
        }

        $user = User::find()->id($userId)->one();

        if (! $user) {
            return null;
        }

        $users[$userId] = $user;

        Ansel::$plugin->getStorageService()->set($users, 'users');

        return $user;
    }

    /**
     * Preloads element for a set of AnselImageModels
     * @param AnselImageModel[] $set
     * @param string[] $toPreload
     */
    public static function preLoadElementsForSet(
        array $set = [],
        array $toPreload = []
    ) {
        if (Craft::$app->getRequest()->getIsLivePreview()) {
            return;
        }

        $available = [
            'userId',
            'assetId',
            'highQualAssetId',
            'thumbAssetId',
            'originalAssetId',
        ];

        if (! $toPreload) {
            $toPreload = $available;
        }

        /**
         * Preload users
         */

        if (\in_array('userId', $toPreload, true)) {
            $users = Ansel::$plugin->getStorageService()->get('users') ?: [];

            $userIds = [];

            foreach ($set as $anselImageModel) {
                if (isset($users[$anselImageModel->userId])) {
                    continue;
                }

                $userIds[$anselImageModel->userId] = $anselImageModel->userId;
            }

            $userQuery = User::find()->id(array_values($userIds))->all();

            foreach ($userQuery as $user) {
                $users[$user->id] = $user;
            }

            Ansel::$plugin->getStorageService()->set($users, 'users');
        }


        /**
         * Preload Assets
         */

        $assets = Ansel::$plugin->getStorageService()->get('assets') ?: [];

        $assetIds = [];

        foreach ($set as $anselImageModel) {
            if ($anselImageModel->assetId &&
                ! isset($assets[$anselImageModel->assetId]) &&
                \in_array('assetId', $toPreload, true)
            ) {
                $assetIds[$anselImageModel->assetId] = $anselImageModel->assetId;
            }

            if ($anselImageModel->highQualAssetId &&
                ! isset($assets[$anselImageModel->highQualAssetId]) &&
                \in_array('highQualAssetId', $toPreload, true)
            ) {
                $assetIds[$anselImageModel->highQualAssetId] = $anselImageModel->highQualAssetId;
            }

            if ($anselImageModel->thumbAssetId &&
                ! isset($assets[$anselImageModel->thumbAssetId]) &&
                \in_array('thumbAssetId', $toPreload, true)
            ) {
                $assetIds[$anselImageModel->thumbAssetId] = $anselImageModel->thumbAssetId;
            }

            if ($anselImageModel->originalAssetId &&
                ! isset($assets[$anselImageModel->originalAssetId]) &&
                \in_array('originalAssetId', $toPreload, true)
            ) {
                $assetIds[$anselImageModel->originalAssetId] = $anselImageModel->originalAssetId;
            }
        }

        if (! $assetIds) {
            return;
        }

        $assetQuery = Asset::find()->id($assetIds)->all();

        foreach ($assetQuery as $asset) {
            $assets[$asset->id] = $asset;
        }

        Ansel::$plugin->getStorageService()->set($assets, 'assets');
    }

    /**
     * Gets base 64 from cache file
     * @throws \Exception
     */
    public function getBase64FromCacheFile()
    {
        $cacheService = Ansel::$plugin->getFileCacheService();
        $cachePath = $cacheService->getCachePath();
        $mimeType = (new \finfo())->file(
            "{$cachePath}/{$this->cacheFile}",
            FILEINFO_MIME_TYPE
        );
        $base64 = "data:image/{$mimeType};base64,";
        $base64 .= base64_encode($cacheService->getCacheFileContents(
            $this->cacheFile
        ));
        return $base64;
    }

    /**
     * Gets a value from the predefined array
     * @param string $key
     * @return mixed
     */
    public function getFromPreManipulationArray(string $key)
    {
        if (isset($this->preManipulation[$key])) {
            return $this->preManipulation[$key];
        }
        return null;
    }
}
