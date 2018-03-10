<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\models;

use felicity\datamodel\Model;
use felicity\datamodel\services\datahandlers\IntHandler;
use felicity\datamodel\services\datahandlers\BoolHandler;
use felicity\datamodel\services\datahandlers\StringHandler;
use felicity\datamodel\services\datahandlers\DateTimeHandler;

/**
 * Class AnselImageModel
 */
class AnselImageModel extends Model
{
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
     * @inheritdoc
     */
    protected function defineHandlers(): array
    {
        return [
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
}
