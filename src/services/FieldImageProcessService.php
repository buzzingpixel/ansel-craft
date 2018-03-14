<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use Gregwar\Image\Image as ImageManipulator;
use buzzingpixel\ansel\models\ProcessedFieldImageModel;

/**
 * Class FieldImageProcessService
 */
class FieldImageProcessService
{
    /** @var FileCacheService $fileCacheService */
    private $fileCacheService;

    /** @var ImageManipulator $imageManipulator */
    private $imageManipulator;

    /**
     * FieldImageProcessService constructor
     * @param FileCacheService $fileCacheService
     * @param ImageManipulator $imageManipulator
     */
    public function __construct(
        FileCacheService $fileCacheService,
        ImageManipulator $imageManipulator
    ) {
        $this->fileCacheService = $fileCacheService;
        $this->imageManipulator = $imageManipulator;

        $this->imageManipulator->setCacheDir(
            $this->fileCacheService->getCachePath()
        );

        // TODO: check if we should be using imagick
        // var_dump(extension_loaded('imagick'));
        // die;
    }

    /**
     * Process the image
     * @param ProcessedFieldImageModel $model
     * @throws \Exception
     */
    public function processImage(ProcessedFieldImageModel $model)
    {
        $outputType = $model->forceJpg ? 'jpeg' : 'guess';

        $imageManipulator = clone $this->imageManipulator;

        $imageManipulator->fromFile($model->getFilePath());

        $imageManipulator->setForceCache();

        $imageManipulator->crop($model->x, $model->y, $model->w, $model->h);

        $ext = $imageManipulator->guessType();
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;

        if ($model->forceJpg) {
            $ext = 'jpg';
        }


        /**
         * Resize the image
         */

        $imageWidth = $model->w;
        $imageHeight = $model->h;

        if (($model->maxWidth && $imageWidth > $model->maxWidth) ||
            ($model->maxHeight && $imageHeight > $model->maxHeight)
        ) {
            $width = $model->maxWidth;
            $height = $model->maxHeight;

            if ($model->maxWidth) {
                $ratio = (float) $model->maxWidth / $imageWidth;
                $height = (int) round($imageHeight * $ratio);
            }

            if ($model->maxHeight && $height > $model->maxHeight) {
                $ratio = (float) $model->maxHeight / $imageHeight;
                $height = $model->maxHeight;
                $width = (int) round($imageWidth * $ratio);
            }

            $imageManipulator->forceResize($width, $height);
        }

        $imageWidth = $model->w;
        $imageHeight = $model->h;


        /**
         * High quality image
         */

        $model->setProperty(
            'highQualityImgCacheLocation',
            $this->fileCacheService->createEmptyFile($ext)
        );

        $imageManipulator->save(
            $model->getHighQualityFilePath(),
            $outputType,
            100
        );


        /**
         * Standard image
         */

        $model->setProperty(
            'standardImgCacheLocation',
            $this->fileCacheService->createEmptyFile($ext)
        );

        $imageManipulator->save(
            $model->getStandardFilePath(),
            $outputType,
            $model->quality
        );

        /**
         * Image thumbnail
         */

        $width = 168;
        $ratio = (float) $width / $imageWidth;
        $height = (int) round($imageHeight * $ratio);

        $imageManipulator->forceResize($width, $height);

        $model->setProperty(
            'thumbImgCacheLocation',
            $this->fileCacheService->createEmptyFile($ext)
        );

        $imageManipulator->save(
            $model->getThumbFilePath(),
            $outputType,
            90
        );
    }
}
