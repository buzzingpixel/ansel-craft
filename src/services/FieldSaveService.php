<?php

namespace buzzingpixel\ansel\services;

use craft\elements\Asset;
use craft\db\Connection as DbConnection;
use craft\helpers\Assets as AssetsHelper;
use craft\services\Elements as ElementsService;
use buzzingpixel\ansel\models\AnselFieldSettingsModel;
use buzzingpixel\ansel\models\ProcessedFieldImageModel;

/**
 * Class SaveFieldFromPostArray
 */
class FieldSaveService
{
    /** @var FileCacheService $fileCacheService */
    private $fileCacheService;

    /** @var FieldImageProcessService $fieldImageProcessService */
    private $fieldImageProcessService;

    /** @var DbConnection $dbConnection */
    private $dbConnection;

    /** @var Asset $newAssetElement */
    private $newAssetElement;

    /** @var AssetsHelper $assetsHelper */
    private $assetsHelper;

    /** @var ElementsService $elementsService */
    private $elementsService;

    /** @var int $userId */
    private $userId;

    /**
     * FieldSaveService constructor
     * @param FileCacheService $fileCacheService
     * @param FieldImageProcessService $fieldImageProcessService
     * @param DbConnection $dbConnection
     * @param Asset $newAssetElement
     * @param AssetsHelper $assetsHelper
     * @param ElementsService $elementsService
     * @param int $userId
     */
    public function __construct(
        FileCacheService $fileCacheService,
        FieldImageProcessService $fieldImageProcessService,
        DbConnection $dbConnection,
        Asset $newAssetElement,
        AssetsHelper $assetsHelper,
        ElementsService $elementsService,
        int $userId
    ) {
        $this->fileCacheService = $fileCacheService;
        $this->fieldImageProcessService = $fieldImageProcessService;
        $this->dbConnection = $dbConnection;
        $this->newAssetElement = $newAssetElement;
        $this->assetsHelper = $assetsHelper;
        $this->elementsService = $elementsService;
        $this->userId = $userId;
    }

    /**
     * Saves a field from post array
     * @param array $postArray = [
     *     [
     *         'title' => 'some title',
     *         'caption' => 'some caption',
     *         'cover' => '1',
     *         'x' => 123,
     *         'y' => 123,
     *         'width' => 123,
     *         'height' => 123,
     *         'cacheFile' => 'path/to/file/in/ansel/cache/dir/file.jpg',
     *         'fileName' => 'proper-file-name-of-cache-file.jpg',
     *         'preManipulation' => [
     *             'h' => 123,
     *             'w' => 123,
     *             'x' => 123,
     *             'y' => 123,
     *             'highQualityImgCacheLocation' => 'path/to/file/in/ansel/cache/dir/file.jpg',
     *             'standardImgCacheLocation' => 'path/to/file/in/ansel/cache/dir/file.jpg',
     *             'thumbImgCacheLocation' => 'path/to/file/in/ansel/cache/dir/file.jpg',
     *         ]
     *     ]
     * ]
     * @param AnselFieldSettingsModel $fieldSettings
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveFieldFromPostArray(
        array $postArray,
        AnselFieldSettingsModel $fieldSettings
    ) {
        unset($postArray['placeholder']);

        if (! $postArray) {
            return;
        }

        $deleteIds = [];

        $pos = 1;

        foreach ($postArray as &$imageArray) {
            $imageArray['position'] = $pos;

            $imageArray['disabled'] = 0;

            if ($fieldSettings->maxQty && $pos > $fieldSettings->maxQty) {
                $imageArray['disabled'] = 1;
            }

            $pos++;
        }

        unset($imageArray);

        foreach ($postArray as $imageArray) {
            $delete = $imageArray['deleteImage'] ?? '0';

            if ($delete === '1') {
                // TODO: add this image to the delete array
                var_dump('// TODO: add this image to the delete array');
                die;
                // $deleteIds[] = $imageArray['id'];
                continue;
            }

            $this->saveFieldImageFromPostArray($imageArray, $fieldSettings);
        }


        // TODO: delete images if there are any images to delete
        if (! $deleteIds) {
            return;
        }

        var_dump('TODO: delete images', $deleteIds);
        die;
    }

    /**
     * Saves an image in a field from post array
     * @param array $postArray See the array documentation in saveFieldFromPostArray method
     * @param AnselFieldSettingsModel $fieldSettings
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveFieldImageFromPostArray(
        array $postArray,
        AnselFieldSettingsModel $fieldSettings
    ) {
        $uniqueId = uniqid('', false);

        /**
         * We need to check if the pre-manipulations had time to run and if they
         * match the values the should be
         */

        $pre = $postArray['preManipulation'] ?? null;
        $preHeight = (int) ($pre['h'] ?? null);
        $preWidth = (int) ($pre['w'] ?? null);
        $preX = (int) ($pre['x'] ?? null);
        $preY = (int) ($pre['y'] ?? null);
        $highQualCacheLoc = $pre['highQualityImgCacheLocation'] ?? null;
        $standardCacheLoc = $pre['standardImgCacheLocation'] ?? null;
        $thumbCacheLoc = $pre['thumbImgCacheLocation'] ?? null;

        $height = (int) $postArray['height'];
        $width = (int) $postArray['width'];
        $x = (int) $postArray['x'];
        $y = (int) $postArray['y'];

        // TODO: check if the image exists and manipulation has changed
        // TODO: we don't need to run manipulations if that's the case

        if ($height !== $preHeight ||
            $width !== $preWidth ||
            $x !== $preX ||
            $y !== $preY ||
            ! $this->fileCacheService->cacheFileExists($highQualCacheLoc) ||
            ! $this->fileCacheService->cacheFileExists($standardCacheLoc) ||
            ! $this->fileCacheService->cacheFileExists($thumbCacheLoc)
        ) {
            $processedFieldImageModel = new ProcessedFieldImageModel([
                'h' => $height,
                'w' => $width,
                'x' => $x,
                'y' => $y,
                'fileLocation' => $postArray['cacheFile'], // TODO: Get the approprate image
                'fileLocationType' => 'cacheFile', // TODO: set this appropriately
                'quality' => $fieldSettings->quality,
                'maxWidth' => $fieldSettings->maxWidth,
                'maxHeight' => $fieldSettings->maxHeight,
                'forceJpg' => $fieldSettings->forceJpg,
            ]);

            $this->fieldImageProcessService->processImage(
                $processedFieldImageModel
            );

            $highQualCacheLoc = $processedFieldImageModel->highQualityImgCacheLocation;
            $standardCacheLoc = $processedFieldImageModel->standardImgCacheLocation;
            $thumbCacheLoc = $processedFieldImageModel->thumbImgCacheLocation;
        }

        // TODO: Check if we need to add the original asset or we're working
        // from an existing asset
        $newAssetFileName = pathinfo($postArray['fileName']);
        $newAssetFileName = $this->assetsHelper::prepareAssetName(
            "{$newAssetFileName['filename']}-{$uniqueId}.{$newAssetFileName['extension']}"
        );

        $cachePath = $this->fileCacheService->getCachePath();

        $originalAssetCacheLoc = "{$cachePath}/{$postArray['cacheFile']}";
        $highQualCacheLoc = "{$cachePath}/{$highQualCacheLoc}";
        $standardCacheLoc = "{$cachePath}/{$standardCacheLoc}";
        $thumbCacheLoc = "{$cachePath}/{$thumbCacheLoc}";

        $originalAsset = clone $this->newAssetElement;
        $originalAsset->tempFilePath = $originalAssetCacheLoc;
        $originalAsset->filename = $newAssetFileName;
        $originalAsset->newFolderId = $fieldSettings->getProperty('uploadFolderId');
        $originalAsset->volumeId = $fieldSettings->getProperty('uploadLocation');
        $originalAsset->avoidFilenameConflicts = true;
        $originalAsset->setScenario($originalAsset::SCENARIO_CREATE);

        $this->elementsService->saveElement($originalAsset);

        $highQualAsset = clone $this->newAssetElement;
        $highQualAsset->tempFilePath = $highQualCacheLoc;
        $highQualAsset->filename = $newAssetFileName;
        $highQualAsset->newFolderId = $fieldSettings->getProperty('highQualFolderId');
        $highQualAsset->volumeId = $fieldSettings->getProperty('saveLocation');
        $highQualAsset->avoidFilenameConflicts = true;
        $highQualAsset->setScenario($originalAsset::SCENARIO_CREATE);

        $this->elementsService->saveElement($highQualAsset);

        $standardAsset = clone $this->newAssetElement;
        $standardAsset->tempFilePath = $standardCacheLoc;
        $standardAsset->filename = $newAssetFileName;
        $standardAsset->newFolderId = $fieldSettings->getProperty('saveFolderId');
        $standardAsset->volumeId = $fieldSettings->getProperty('saveLocation');
        $standardAsset->avoidFilenameConflicts = true;
        $standardAsset->setScenario($originalAsset::SCENARIO_CREATE);

        $this->elementsService->saveElement($standardAsset);

        $thumbAsset = clone $this->newAssetElement;
        $thumbAsset->tempFilePath = $thumbCacheLoc;
        $thumbAsset->filename = $newAssetFileName;
        $thumbAsset->newFolderId = $fieldSettings->getProperty('thumbFolderId');
        $thumbAsset->volumeId = $fieldSettings->getProperty('saveLocation');
        $thumbAsset->avoidFilenameConflicts = true;
        $thumbAsset->setScenario($originalAsset::SCENARIO_CREATE);

        $this->elementsService->saveElement($thumbAsset);

        // TODO: check for existing image row

        $cover = $postArray['cover'] ?? '';
        $cover = $cover === '1' || $cover === 1 ? 1 : 0;
        $disabled = $postArray['disabled'] ?? '';
        $disabled = $disabled === '1' || $disabled === 1 ? 1 : 0;

        $this->dbConnection->createCommand()->upsert(
            '{{%anselImages}}',
            [
                'id' => null, // TODO: Real ID here if exists/applicable
                'elementId' => $fieldSettings->elementId,
                'fieldId' => $fieldSettings->fieldId,
                'userId' => $this->userId,
                'assetId' => $standardAsset->id,
                'highQualAssetId' => $highQualAsset->id,
                'thumbAssetId' => $thumbAsset->id,
                'originalAssetId' => $originalAsset->id,
                'width' => $width,
                'height' => $height,
                'x' => $x,
                'y' => $y,
                'title' => $postArray['title'] ?? '',
                'caption' => $postArray['caption'] ?? '',
                'cover' => $cover,
                'position' => $postArray['position'] ?? 1,
                'disabled' => $disabled,
            ]
        )
        ->execute();
    }
}
