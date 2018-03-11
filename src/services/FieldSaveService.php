<?php

namespace buzzingpixel\ansel\services;

use craft\db\Query;
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

    /** @var Query $query */
    private $query;

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

    /** @var array $existingImageQueries */
    private $existingImageQueries = [];

    /**
     * FieldSaveService constructor
     * @param FileCacheService $fileCacheService
     * @param FieldImageProcessService $fieldImageProcessService
     * @param Query $query
     * @param DbConnection $dbConnection
     * @param Asset $newAssetElement
     * @param AssetsHelper $assetsHelper
     * @param ElementsService $elementsService
     * @param int $userId
     */
    public function __construct(
        FileCacheService $fileCacheService,
        FieldImageProcessService $fieldImageProcessService,
        Query $query,
        DbConnection $dbConnection,
        Asset $newAssetElement,
        AssetsHelper $assetsHelper,
        ElementsService $elementsService,
        int $userId
    ) {
        $this->fileCacheService = $fileCacheService;
        $this->fieldImageProcessService = $fieldImageProcessService;
        $this->query = $query;
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

        $imageIds = [];

        $pos = 1;

        foreach ($postArray as &$imageArray) {
            $id = (int) ($imageArray['id'] ?? null);

            if ($id) {
                $imageIds[] = $id;
            }

            $imageArray['position'] = $pos;

            $imageArray['disabled'] = 0;

            if ($fieldSettings->maxQty && $pos > $fieldSettings->maxQty) {
                $imageArray['disabled'] = 1;
            }

            $pos++;
        }

        unset($imageArray);

        if ($imageIds) {
            $imageIds = implode(',', $imageIds);
            $existingImageQuery = (clone $this->query)->from('{{%anselImages}}')
                ->where("`id` IN ({$imageIds})")
                ->all();

            foreach ($existingImageQuery as $item) {
                $this->existingImageQueries[$item['id']] = $this->castRowVars(
                    $item
                );
            }
        }

        foreach ($postArray as $imageArray) {
            $this->saveFieldImageFromPostArray($imageArray, $fieldSettings);
        }
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
        /**
         * Check for an existing image row
         */

        $existingId = (int) ($postArray['id'] ?? null);
        $existingRow = null;

        if ($existingId) {
            $existingRow = $this->existingImageQueries[$existingId] ?? null;
        }

        if (! $existingRow && $existingId) {
            $query = (clone $this->query)->from('{{%anselImages}}')
                ->where("`id` = {$existingId}")
                ->one();

            $existingRow = $this->castRowVars($query);
        }

        $delete = $postArray['delete'] ?? '0';

        $oldAssetIds = [];

        if (($delete === '1' || $delete === 1) && $existingRow) {
            if ($existingRow['assetId']) {
                $oldAssetIds[] = $existingRow['assetId'];
            }

            if ($existingRow['highQualAssetId']) {
                $oldAssetIds[] = $existingRow['highQualAssetId'];
            }

            if ($existingRow['thumbAssetId']) {
                $oldAssetIds[] = $existingRow['thumbAssetId'];
            }

            $this->dbConnection->createCommand()
                ->delete(
                    '{{%anselImages}}',
                    "`id` = {$existingRow['id']}"
                )
                ->execute();

            if (! $oldAssetIds) {
                return;
            }

            foreach ($oldAssetIds as $id) {
                $this->elementsService->deleteElementById($id);
            }

            return;
        }

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

        $originalAssetId = (int) $postArray['originalAssetId'];

        $hasChanged = empty($existingRow);

        if ($existingRow['originalAssetId'] !== $originalAssetId) {
            $hasChanged = true;
        }

        if ($existingRow['height'] !== $height) {
            $hasChanged = true;
        }

        if ($existingRow['width'] !== $width) {
            $hasChanged = true;
        }

        if ($existingRow['x'] !== $x) {
            $hasChanged = true;
        }

        if ($existingRow['y'] !== $y) {
            $hasChanged = true;
        }

        /**
         * $hasChanged tells us whether we need to run manipulations. If the
         * file already existed and nothing changed, we don't need to
         * be running any manipulations
         */
        if ($hasChanged) {
            if ($existingRow['assetId']) {
                $oldAssetIds[] = $existingRow['assetId'];
            }

            if ($existingRow['highQualAssetId']) {
                $oldAssetIds[] = $existingRow['highQualAssetId'];
            }

            if ($existingRow['thumbAssetId']) {
                $oldAssetIds[] = $existingRow['thumbAssetId'];
            }

            /**
             * We need to make sure the form was not submitted before all the
             * pre-processing of manipulations ran. If the pre-manipulation
             * values don't match the expected values, we need to run the
             * manipulations
             */

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
                    'fileLocation' => $originalAssetId ?: $postArray['cacheFile'],
                    'fileLocationType' =>$originalAssetId ? 'asset' : 'cacheFile',
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

            $originalAsset = null;
            $originalAssetFileName = null;

            if ($originalAssetId) {
                $originalAsset = $this->newAssetElement::find()->id($originalAssetId)->one();

                if ($originalAsset) {
                    $originalAssetFileName = $originalAsset->getFilename();
                }
            }

            if (! $originalAssetFileName) {
                $originalAssetFileName = $postArray['fileName'] ?? false;
                $originalAssetFileName = $originalAssetFileName ?: uniqid('', false);
            }

            $newAssetFileName = pathinfo($originalAssetFileName);

            $newAssetFileName = $this->assetsHelper::prepareAssetName(
                "{$newAssetFileName['filename']}-{$uniqueId}.{$newAssetFileName['extension']}"
            );

            $cachePath = $this->fileCacheService->getCachePath();

            $highQualCacheLoc = "{$cachePath}/{$highQualCacheLoc}";
            $standardCacheLoc = "{$cachePath}/{$standardCacheLoc}";
            $thumbCacheLoc = "{$cachePath}/{$thumbCacheLoc}";

            if (! $originalAsset) {
                $originalAssetCacheLoc = "{$cachePath}/{$postArray['cacheFile']}";
                $originalAsset = clone $this->newAssetElement;
                $originalAsset->tempFilePath = $originalAssetCacheLoc;
                $originalAsset->filename = $originalAssetFileName;
                $originalAsset->newFolderId = $fieldSettings->getProperty('uploadFolderId');
                $originalAsset->volumeId = $fieldSettings->getProperty('uploadLocation');
                $originalAsset->avoidFilenameConflicts = true;
                $originalAsset->setScenario($originalAsset::SCENARIO_CREATE);

                $this->elementsService->saveElement($originalAsset);
            }

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
        }

        $cover = $postArray['cover'] ?? '';
        $cover = $cover === '1' || $cover === 1 ? 1 : 0;
        $disabled = $postArray['disabled'] ?? '';
        $disabled = $disabled === '1' || $disabled === 1 ? 1 : 0;

        $this->dbConnection->createCommand()->upsert(
            '{{%anselImages}}',
            [
                'id' => $existingRow['id'] ?? null,
                'elementId' => $fieldSettings->elementId,
                'fieldId' => $fieldSettings->fieldId,
                'userId' => $existingRow['userId'] ?? $this->userId,
                'assetId' => $standardAsset->id ?? $existingRow['assetId'],
                'highQualAssetId' => $highQualAsset->id ?? $existingRow['highQualAssetId'],
                'thumbAssetId' => $thumbAsset->id ?? $existingRow['thumbAssetId'],
                'originalAssetId' => $originalAsset->id ?? $existingRow['originalAssetId'],
                'width' => $width,
                'height' => $height,
                'x' => $x,
                'y' => $y,
                'title' => $postArray['title'] ?? $existingRow['title'] ?? '',
                'caption' => $postArray['caption'] ?? $existingRow['caption'] ?? '',
                'cover' => $cover,
                'position' => $postArray['position'] ?? $existingRow['position'] ?? 1,
                'disabled' => $disabled,
            ]
        )
        ->execute();

        if (! $oldAssetIds) {
            return;
        }

        foreach ($oldAssetIds as $id) {
            $this->elementsService->deleteElementById($id);
        }
    }

    /**
     * Casts row variables
     * @param null $row
     * @return null|array
     */
    private function castRowVars($row = null)
    {
        if (! $row) {
            return null;
        }

        $newRow = [];

        $newRow['id'] = (int) ($row['id'] ?? null);
        $newRow['elementId'] = (int) ($row['elementId'] ?? null);
        $newRow['fieldId'] = (int) ($row['fieldId'] ?? null);
        $newRow['userId'] = (int) ($row['userId'] ?? null);
        $newRow['assetId'] = (int) ($row['assetId'] ?? null);
        $newRow['highQualAssetId'] = (int) ($row['highQualAssetId'] ?? null);
        $newRow['thumbAssetId'] = (int) ($row['thumbAssetId'] ?? null);
        $newRow['originalAssetId'] = (int) ($row['originalAssetId'] ?? null);
        $newRow['width'] = (int) ($row['width'] ?? null);
        $newRow['height'] = (int) ($row['height'] ?? null);
        $newRow['x'] = (int) ($row['x'] ?? null);
        $newRow['y'] = (int) ($row['y'] ?? null);
        $newRow['title'] = (string) ($row['title'] ?? null);
        $newRow['caption'] = (string) ($row['caption'] ?? null);
        $newRow['cover'] = $row['cover'] === '1' || $row['cover'] === 1;
        $newRow['position'] = (int) ($row['position'] ?? null);
        $newRow['disabled'] = $row['disabled'] === '1' || $row['disabled'] === 1;

        return $newRow;
    }
}
