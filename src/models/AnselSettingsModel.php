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

    /** @var bool $optimizerShowErrors */
    public $optimizerShowErrors = false;

    /** @var bool $disableOptipng */
    public $disableOptipng = false;

    /** @var bool $disableJpegoptim */
    public $disableJpegoptim = false;

    /** @var bool $disableGifsicle */
    public $disableGifsicle = false;

    /**
     * Gets config settings hidden from the settings page
     * @return array
     */
    public function getHiddenConfigSettings() : array
    {
        return [
            'defaultHost' => 'defaultHost',
            'optimizerShowErrors' => 'optimizerShowErrors',
            'disableOptipng' => 'disableOptipng',
            'disableJpegoptim' => 'disableJpegoptim',
            'disableGifsicle' => 'disableGifsicle',
        ];
    }

    /**
     * Gets property type
     * @param string $prop
     * @return string
     */
    public function getPropertyType(string $prop) : string
    {
        $types = [
            'defaultHost' => 'string',
            'defaultMaxQty' => 'int',
            'defaultImageQuality' => 'int',
            'defaultJpg' => 'bool',
            'defaultRetina' => 'bool',
            'defaultShowTitle' => 'bool',
            'defaultRequireTitle' => 'bool',
            'defaultTitleLabel' => 'string',
            'defaultShowCaption' => 'bool',
            'defaultRequireCaption' => 'bool',
            'defaultCaptionLabel' => 'string',
            'defaultShowCover' => 'bool',
            'defaultRequireCover' => 'bool',
            'defaultCoverLabel' => 'string',
            'hideSourceSaveInstructions' => 'bool',
        ];

        return $types[$prop] ?? '';
    }

    /**
     * Gets property type
     * @param string $prop
     * @return string
     */
    public function getInstructions(string $prop) : string
    {
        $types = [
            'defaultHost' => '',
            'defaultMaxQty' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultImageQuality' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultJpg' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultRetina' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultShowTitle' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultRequireTitle' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultTitleLabel' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultShowCaption' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultRequireCaption' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultCaptionLabel' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultShowCover' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultRequireCover' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'defaultCoverLabel' => 'Default value when creating new Ansel fields (does not affect existing fields or prevent setting higher or lower max quantity)',
            'hideSourceSaveInstructions' => 'When set to no, a brief explanation of how to make best use of the Upload/Save location paradigm will appear above those options when creating a new field. If you already know how this works it can be annoying and you may wish to hide it.',
        ];

        return $types[$prop] ?? '';
    }

    /**
     * Gets a property's label
     * @param string $prop
     * @return string
     */
    public function getLabel(string $prop) : string
    {
        $types = [
            'defaultHost' => 'Default host',
            'defaultMaxQty' => 'Default maximum quantity',
            'defaultImageQuality' => 'Default image quality',
            'defaultJpg' => 'Default force JPG setting',
            'defaultRetina' => 'Default retina mode',
            'defaultShowTitle' => 'Default display title field',
            'defaultRequireTitle' => 'Default require title field',
            'defaultTitleLabel' => 'Default customize title label',
            'defaultShowCaption' => 'Default display caption field',
            'defaultRequireCaption' => 'Default require caption field',
            'defaultCaptionLabel' => 'Default customize caption label',
            'defaultShowCover' => 'Default display cover field',
            'defaultRequireCover' => 'Default require cover field',
            'defaultCoverLabel' => 'Default customize cover label',
            'hideSourceSaveInstructions' => 'Hide the Upload/Save location instructions when setting up a new field?',
        ];

        return $types[$prop] ?? '';
    }

    /**
     * @inheritdoc
     */
    protected function defineHandlers(): array
    {
        return [
            'defaultHost' => ['class' => StringHandler::class],
            'defaultMaxQty' => ['class' => IntHandler::class],
            'defaultImageQuality' => [
                'class' => IntHandler::class,
                'min' => 1,
                'max' => 100,
            ],
            'defaultJpg' => ['class' => BoolHandler::class],
            'defaultRetina' => ['class' => BoolHandler::class],
            'defaultShowTitle' => ['class' => BoolHandler::class],
            'defaultRequireTitle' => ['class' => BoolHandler::class],
            'defaultTitleLabel' => ['class' => StringHandler::class],
            'defaultShowCaption' => ['class' => BoolHandler::class ],
            'defaultRequireCaption' => ['class' => BoolHandler::class],
            'defaultCaptionLabel' => ['class' => StringHandler::class],
            'defaultShowCover' => ['class' => BoolHandler::class],
            'defaultRequireCover' => ['class' => BoolHandler::class],
            'defaultCoverLabel' => ['class' => StringHandler::class],
            'hideSourceSaveInstructions' => ['class' => BoolHandler::class],
            'optimizerShowErrors' => ['class' => BoolHandler::class],
            'disableOptipng' => ['class' => BoolHandler::class],
            'disableJpegoptim' => ['class' => BoolHandler::class],
            'disableGifsicle' => ['class' => BoolHandler::class],
        ];
    }
}
