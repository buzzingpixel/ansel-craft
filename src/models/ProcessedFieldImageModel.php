<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\models;

use buzzingpixel\ansel\Ansel;
use felicity\datamodel\Model;
use felicity\datamodel\services\datahandlers\IntHandler;
use felicity\datamodel\services\datahandlers\BoolHandler;
use felicity\datamodel\services\datahandlers\StringHandler;

/**
 * Class ProcessedFieldImageModel
 */
class ProcessedFieldImageModel extends Model
{
    /** @var int $h */
    public $h;

    /** @var int $w */
    public $w;

    /** @var int $x */
    public $x;

    /** @var int $y */
    public $y;

    /** @var int $maxWidth */
    public $maxWidth;

    /** @var int $maxHeight */
    public $maxHeight;

    /** @var int $quality */
    public $quality;

    /** @var bool $forceJpg */
    public $forceJpg;

    /** @var string $fileLocation */
    public $fileLocation;

    /** @var string $fileLocationType */
    public $fileLocationType;

    /** @var string $highQualityImgCacheLocation */
    public $highQualityImgCacheLocation;

    /** @var string $standardImgCacheLocation */
    public $standardImgCacheLocation;

    /** @var string $thumbImgCacheLocation */
    public $thumbImgCacheLocation;

    /**
     * @inheritdoc
     */
    protected function defineHandlers(): array
    {
        return [
            'h' => ['class' => IntHandler::class],
            'w' => ['class' => IntHandler::class],
            'x' => ['class' => IntHandler::class],
            'y' => ['class' => IntHandler::class],
            'maxWidth' => ['class' => IntHandler::class],
            'maxHeight' => ['class' => IntHandler::class],
            'quality' => ['class' => IntHandler::class],
            'forceJpg' => ['class' => BoolHandler::class],
            'fileLocation' => ['class' => StringHandler::class],
            'fileLocationType' => ['class' => StringHandler::class],
            'highQualityImgCacheLocation' => ['class' => StringHandler::class],
            'standardImgCacheLocation' => ['class' => StringHandler::class],
            'thumbImgCacheLocation' => ['class' => StringHandler::class],
        ];
    }

    /**
     * Gets the file path
     * @return string
     * @throws \Exception
     */
    public function getFilePath() : string
    {
        // TODO: get the file path from the right place based on the fileLocationType property
        $path = Ansel::$plugin->getFileCacheService()->getCachePath();
        return "{$path}/{$this->fileLocation}";
    }

    /**
     * Gets the high quality file path
     * @return string
     * @throws \Exception
     */
    public function getHighQualityFilePath() : string
    {
        $path = Ansel::$plugin->getFileCacheService()->getCachePath();
        return "{$path}/{$this->highQualityImgCacheLocation}";
    }

    /**
     * Gets the standard quality file path
     * @return string
     * @throws \Exception
     */
    public function getStandardFilePath() : string
    {
        $path = Ansel::$plugin->getFileCacheService()->getCachePath();
        return "{$path}/{$this->standardImgCacheLocation}";
    }

    /**
     * Gets the thumb file path
     * @return string
     * @throws \Exception
     */
    public function getThumbFilePath() : string
    {
        $path = Ansel::$plugin->getFileCacheService()->getCachePath();
        return "{$path}/{$this->thumbImgCacheLocation}";
    }
}
