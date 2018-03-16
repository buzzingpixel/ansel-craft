<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\AbstractImagine;
use buzzingpixel\ansel\models\ProcessedFieldImageModel;

/**
 * Class FieldImageProcessService
 */
class FieldImageProcessService
{
    /** @var FileCacheService $fileCacheService */
    private $fileCacheService;

    /** @var AbstractImagine $imageManipulator */
    private $imageManipulator;

    /**
     * FieldImageProcessService constructor
     * @param FileCacheService $fileCacheService
     * @param AbstractImagine $imageManipulator
     * @throws \Exception
     */
    public function __construct(
        FileCacheService $fileCacheService,
        AbstractImagine $imageManipulator
    ) {
        $this->fileCacheService = $fileCacheService;
        $this->imageManipulator = $imageManipulator;
    }

    /**
     * Process the image
     * @param ProcessedFieldImageModel $model
     * @throws \Exception
     */
    public function processImage(ProcessedFieldImageModel $model)
    {
        $image = $this->imageManipulator->open($model->getFilePath());

        $image->crop(
            new Point($model->x, $model->y),
            new Box($model->w, $model->h)
        );

        $ext = pathinfo($model->getFilePath())['extension'];
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

            $image->resize(new Box($width, $height));
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

        $image->save(
            $model->getHighQualityFilePath(),
            [
                'jpeg_quality' => 100,
                'png_compression_level' => 0,
            ]
        );


        /**
         * Standard image
         */

        $model->setProperty(
            'standardImgCacheLocation',
            $this->fileCacheService->createEmptyFile($ext)
        );

        $pngCompressionLevel = 0;

        if ($model->quality < 90) {
            $pngCompressionLevel = 1;
        }

        if ($model->quality < 80) {
            $pngCompressionLevel = 2;
        }

        if ($model->quality < 70) {
            $pngCompressionLevel = 3;
        }

        if ($model->quality < 60) {
            $pngCompressionLevel = 4;
        }

        if ($model->quality > 49) {
            $pngCompressionLevel = 5;
        }

        if ($model->quality < 50) {
            $pngCompressionLevel = 6;
        }

        if ($model->quality < 40) {
            $pngCompressionLevel = 7;
        }

        if ($model->quality < 30) {
            $pngCompressionLevel = 7;
        }

        if ($model->quality < 20) {
            $pngCompressionLevel = 8;
        }

        if ($model->quality < 10) {
            $pngCompressionLevel = 9;
        }

        $image->save(
            $model->getStandardFilePath(),
            [
                'jpeg_quality' => $model->quality,
                'png_compression_level' => $pngCompressionLevel,
            ]
        );

        /**
         * Image thumbnail
         */

        $width = 168;
        $ratio = (float) $width / $imageWidth;
        $height = (int) round($imageHeight * $ratio);

        $image->resize(new Box($width, $height));

        $model->setProperty(
            'thumbImgCacheLocation',
            $this->fileCacheService->createEmptyFile($ext)
        );

        $image->save(
            $model->getThumbFilePath(),
            [
                'jpeg_quality' => $model->quality,
                'png_compression_level' => $pngCompressionLevel,
            ]
        );
    }
}
