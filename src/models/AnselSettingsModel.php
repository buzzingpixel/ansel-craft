<?php

namespace buzzingpixel\ansel\models;

use felicity\datamodel\Model;
use felicity\datamodel\services\datahandlers\IntHandler;
use felicity\datamodel\services\datahandlers\BoolHandler;
use felicity\datamodel\services\datahandlers\StringHandler;

/**
 * Class AnselSettingsModel
 */
class AnselSettingsModel extends Model
{
    /** @var string $defaultHost */
    public $defaultHost;

    /** @var int $defaultMaxQty */
    public $defaultMaxQty;

    /** @var int $defaultImageQuality */
    public $defaultImageQuality;

    /** @var bool $defaultJpg */
    public $defaultJpg;

    /** @var bool $defaultRetina */
    public $defaultRetina;

    /** @var bool $defaultShowTitle */
    public $defaultShowTitle;

    /** @var bool $defaultRequireTitle */
    public $defaultRequireTitle;

    /** @var string $defaultTitleLabel */
    public $defaultTitleLabel;

    /** @var bool $defaultShowCaption */
    public $defaultShowCaption;

    /** @var bool $defaultRequireCaption */
    public $defaultRequireCaption;

    /** @var string $defaultCaptionLabel */
    public $defaultCaptionLabel;

    /** @var bool $defaultShowCover */
    public $defaultShowCover;

    /** @var bool $defaultRequireCover */
    public $defaultRequireCover;

    /** @var string $defaultCoverLabel */
    public $defaultCoverLabel;

    /** @var bool $hideSourceSaveInstructions */
    public $hideSourceSaveInstructions;

    /**
     * @inheritdoc
     */
    protected function defineHandlers(): array
    {
        return [
            'defaultHost' => [
                'class' => StringHandler::class,
            ],
            'defaultMaxQty' => [
                'class' => IntHandler::class,
            ],
            'defaultImageQuality' => [
                'class' => IntHandler::class,
                'min' => 1,
                'max' => 100,
            ],
            'defaultJpg' => [
                'class' => BoolHandler::class,
            ],
            'defaultRetina' => [
                'class' => BoolHandler::class,
            ],
            'defaultShowTitle' => [
                'class' => BoolHandler::class,
            ],
            'defaultRequireTitle' => [
                'class' => BoolHandler::class,
            ],
            'defaultTitleLabel' => [
                'class' => StringHandler::class,
            ],
            'defaultShowCaption' => [
                'class' => BoolHandler::class,
            ],
            'defaultRequireCaption' => [
                'class' => BoolHandler::class,
            ],
            'defaultCaptionLabel' => [
                'class' => StringHandler::class,
            ],
            'defaultShowCover' => [
                'class' => BoolHandler::class,
            ],
            'defaultRequireCover' => [
                'class' => BoolHandler::class,
            ],
            'defaultCoverLabel' => [
                'class' => StringHandler::class,
            ],
            'hideSourceSaveInstructions' => [
                'class' => BoolHandler::class,
            ],
        ];
    }
}
