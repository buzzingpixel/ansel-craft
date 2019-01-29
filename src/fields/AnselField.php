<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\fields;

use Craft;
use Minify_HTML;
use yii\db\Schema;
use craft\base\Field;
use craft\base\Element;
use craft\helpers\UrlHelper;
use buzzingpixel\ansel\Ansel;
use craft\base\ElementInterface;
use buzzingpixel\ansel\AnselAssetBundle;
use craft\volumes\Local as LocalVolumeType;
use buzzingpixel\ansel\models\AnselImageModel;
use buzzingpixel\ansel\models\AnselSettingsModel;
use \buzzingpixel\ansel\services\AnselImageService;
use buzzingpixel\ansel\models\AnselFieldSettingsModel;

/**
 * Class Ansel
 */
class AnselField extends Field
{
    /**
     * Irritatingly, public properties on the class represent field settings
     */

    /** @var int $uploadLocation */
    public $uploadLocation;

    /** @var int $saveLocation */
    public $saveLocation;

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

    /** @var AnselSettingsModel $settingsModel */
    private $fieldSettingsModel;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'Ansel');
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Runs before field settings save
     * @param bool $isNew
     * @return bool
     * @throws \Exception
     */
    public function beforeSave(bool $isNew): bool
    {
        parent::beforeSave($isNew);
        return $this->getSettingsModel()->validate();
    }

    /**
     * Get's the settings model
     * @throws \Exception
     */
    public function getSettingsModel()
    {
        if (! $this->fieldSettingsModel) {
            $this->fieldSettingsModel = new AnselFieldSettingsModel();
            $this->fieldSettingsModel->setProperty('fieldId', $this->id);
            $this->fieldSettingsModel->setProperty('fieldName', $this->handle);

            foreach ($this->getSettings() as $key => $val) {
                if (! $val) {
                    continue;
                }

                if ($key === 'uploadLocation') {
                    $this->fieldSettingsModel->setUploadLocationFromUid($val);
                    continue;
                }

                if ($key === 'saveLocation') {
                    $this->fieldSettingsModel->setSaveLocationFromUid($val);
                    continue;
                }

                $this->fieldSettingsModel->setProperty($key, $val);
            }

            if ($this->required === '1' &&
                $this->fieldSettingsModel->minQty < 1
            ) {
                $this->fieldSettingsModel->setProperty('minQty', 1);
            }
        }

        return $this->fieldSettingsModel;
    }

    /**
     * Gets the Ansel field type settings HTML
     * @return string
     * @throws \Exception
     */
    public function getSettingsHtml() : string
    {
        $volumeSelectOptions = [[
            'label' => Craft::t('app', 'Choose Location...'),
            'name' => '',
            'value' => '',
        ]];

        foreach (Craft::$app->getVolumes()->getAllVolumes() as $volume) {
            /** @var LocalVolumeType $volume */
            $volumeSelectOptions[$volume->name] = [
                'label' => $volume->name,
                'name' => $volume->id,
                'value' => $volume->uid,
            ];
        }

        ksort($volumeSelectOptions);

        return Craft::$app->getView()->renderTemplate(
            'ansel/_core/FieldSettings.twig',
            [
                'settings' => Ansel::$plugin->getAnselSettingsService()->getSettings(),
                'volumeSelectOptions' => $volumeSelectOptions,
                'fieldSettingsModel' => $this->getSettingsModel(),
            ]
        );
    }

    /**
     * Gets the input HTML for displaying the Ansel field
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return string
     * @throws \Exception
     */
    public function getInputHtml($value, ElementInterface $element = null) : string
    {
        Craft::$app->getView()->registerAssetBundle(AnselAssetBundle::class);
        $settings = $this->getSettingsModel();
        $settings->retinizeValues();

        $images = [];

        if ($element) {
            // Deal with postback
            $values = $element->getFieldValue($this->handle);
            $isPostback = \is_array($values);

            if ($isPostback) {
                unset($values['placeholder']);

                foreach ($values as $item) {
                    $images[] = new AnselImageModel($item);
                }
            } elseif ($value instanceof AnselImageService) {
                $images = $value->showDisabled()
                    ->all();
            }
        }

        AnselImageModel::preLoadElementsForSet($images, ['originalAssetId']);

        Craft::$app->getView()->registerJs(
            '(function() {' .
                "'use strict';" .
                'if (! window.ANSEL) {' .
                    'return;' .
                '}' .
                'window.ANSEL.runInitOnFields();' .
            '})();'
        );

        return Minify_HTML::minify(
            Craft::$app->getView()->renderTemplate('ansel/_field/Index.twig', [
                'uploadKey' => Ansel::$plugin->getUploadKeysService()->createNew(),
                'uploadActionUrl' => UrlHelper::actionUrl('ansel/field-upload/upload'),
                'processActionUrl' => UrlHelper::actionUrl('ansel/image-process/process'),
                'translateActionUrl' => UrlHelper::actionUrl('ansel/translation/translate'),
                'csrfToken' => Craft::$app->getRequest()->getCsrfToken(),
                'settings' => $settings,
                'images' => $images,
            ])
        );
    }

    /**
     * Prepares field data for use in templates
     * @param $value
     * @param ElementInterface|null $element
     * @return null|array|AnselImageService
     * @throws \Exception
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        $settings = $this->getSettingsModel();

        if (\is_array($value)) {
            if (Craft::$app->getRequest()->getIsLivePreview()) {
                unset($value['placeholder']);

                $images = [];

                $maxQty = $settings->maxQty;

                $counter = 1;

                foreach ($value as $item) {
                    $image = new AnselImageModel($item);

                    $image->isLivePreview = true;
                    $image->setProperty('fieldId', $settings->fieldId);
                    $image->setProperty('userId', Craft::$app->getUser()->getId());

                    if ($element) {
                        $image->setProperty('elementId', $element->getId());
                    }

                    if ($image->delete === '1') {
                        continue;
                    }

                    if ($maxQty && $counter > $maxQty) {
                        $image->disabled = true;
                    }

                    $images[] = $image;

                    $counter++;
                }

                return Ansel::$plugin->getAnselImageServiceLivePreview()
                    ->setFieldArray($images);
            }

            return $value;
        }

        if (! $element) {
            return null;
        }

        return Ansel::$plugin->getAnselImageService()
            ->elementId($element->getId())
            ->fieldId($settings->fieldId)
            ->order('position asc');
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        $rules = parent::getElementValidationRules();
        $rules[] = 'validateField';
        return $rules;
    }

    /**
     * Validates the field
     * @param ElementInterface $element
     * @throws \Exception
     */
    public function validateField(ElementInterface $element)
    {
        /** @var Element $element */

        $values = $element->getFieldValue($this->handle);

        if ($values instanceof  AnselImageService) {
            return;
        }

        unset($values['placeholder']);

        foreach ($values as $key => $val) {
            $delete = $val['delete'] ?? null;

            if (! $delete) {
                continue;
            }

            unset($values[$key]);
        }

        $settings = $this->getSettingsModel();

        $totalImages = \count($values);

        if ($totalImages < $settings->minQty) {
            $plural = $settings->minQty > 1 ? 'images' : 'image';
            $element->addError(
                $this->handle,
                Craft::t('app', "You must add at least {count} {$plural}", [
                    'count' => $settings->minQty,
                ])
            );
        }

        if ($totalImages < 1) {
            return;
        }

        if ($settings->requireTitle) {
            $hasValues = true;

            foreach ($values as $value) {
                $val = $value['title'] ?? null;
                if ($val) {
                    continue;
                }
                $hasValues = false;
                break;
            }

            if (! $hasValues) {
                $element->addError(
                    $this->handle,
                    Craft::t('app', 'The "{fieldName}" field is required for each image', [
                        'fieldName' => $settings->titleLabel ?: 'Title',
                    ])
                );
            }
        }

        if ($settings->requireCaption) {
            $hasValues = true;

            foreach ($values as $value) {
                $val = $value['caption'] ?? null;
                if ($val) {
                    continue;
                }
                $hasValues = false;
                break;
            }

            if (! $hasValues) {
                $element->addError(
                    $this->handle,
                    Craft::t('app', 'The "{fieldName}" field is required for each image', [
                        'fieldName' => $settings->captionLabel ?: 'Caption',
                    ])
                );
            }
        }

        if ($settings->requireCover) {
            $coverSet = false;

            foreach ($values as $value) {
                $val = $value['cover'] ?? null;
                if ($val !== '1') {
                    continue;
                }
                $coverSet = true;
                break;
            }

            if (! $coverSet) {
                $element->addError(
                    $this->handle,
                    Craft::t('app', 'The "{fieldName}" field must be set on one image', [
                        'fieldName' => $settings->coverLabel ?: 'Caption',
                    ])
                );
            }
        }
    }

    /**
     * Manipulates Ansel field data after the containing element is saved
     * @param ElementInterface $element
     * @param bool $isNew
     * @throws \Exception
     * @throws \Throwable
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        /** @var Element $element */

        $values = $element->getFieldValue($this->handle);

        if ($values instanceof  AnselImageService) {
            return;
        }

        $settings = $this->getSettingsModel();
        $settings->setProperty('elementId', $element->getId());

        Ansel::$plugin->getFieldSaveService()->saveFieldFromPostArray(
            $values,
            $settings
        );
    }

    /**
     * @param ElementInterface $element
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     */
    public function beforeElementDelete(ElementInterface $element) : bool
    {
        /** @var Element $element */

        Ansel::$plugin->getFieldSaveService()->deleteByElementId(
            $element->getId()
        );

        return true;
    }
}
