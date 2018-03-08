<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel;

use Craft;
use craft\db\Query;
use yii\base\Event;
use Ramsey\Uuid\Uuid;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\services\Fields;
use \craft\helpers\UrlHelper;
use League\Flysystem\Filesystem;
use craft\events\RegisterUrlRulesEvent;
use buzzingpixel\ansel\fields\AnselField;
use Gregwar\Image\Image as ImageManipulator;
use craft\events\RegisterComponentTypesEvent;
use buzzingpixel\ansel\services\FileCacheService;
use buzzingpixel\ansel\services\UploadKeysService;
use buzzingpixel\ansel\services\AnselSettingsService;
use buzzingpixel\ansel\twigextensions\AnselTwigExtension;
use buzzingpixel\ansel\services\FieldImageProcessService;
use buzzingpixel\ansel\controllers\FieldDisplayController;
use League\Flysystem\Adapter\Local as LocalFilesystemAdapter;

/**
 * Class Ansel
 */
class Ansel extends Plugin
{
    /** @var Ansel $plugin */
    public static $plugin;

    /**
     * Initialize plugin
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        Craft::setAlias('@ansel', __DIR__);

        self::$plugin = $this;

        Craft::$app->view->registerTwigExtension(
            new AnselTwigExtension()
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['ansel'] = 'ansel/cp-settings/index';
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = AnselField::class;
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        return Craft::$app->controller->redirect(UrlHelper::cpUrl('ansel'));
    }

    /**
     * We have to have this here because reasons (Craft has its faults)
     */
    public function createSettingsModel()
    {
        return new Noop();
    }

    /**
     * Gets dependency injected AnselSettingsService
     * @return AnselSettingsService
     */
    public function getAnselSettingsService() : AnselSettingsService
    {
        return new AnselSettingsService(
            new Query(),
            Craft::$app->getDb(),
            Craft::$app->getConfig()->getConfigFromFile('ansel')
        );
    }

    /**
     * Gets dependency injected FieldDisplayController
     * @return FieldDisplayController
     */
    public function getFieldDisplayController() : FieldDisplayController
    {
        return new FieldDisplayController(
            uniqid('', false),
            $this
        );
    }

    /**
     * Gets dependency injected UploadKeysService
     * @return UploadKeysService
     * @throws \Exception
     */
    public function getUploadKeysService() : UploadKeysService
    {
        return new UploadKeysService(
            new Query(),
            Craft::$app->getDb()
        );
    }

    /** @var FileCacheService $fileCacheService */
    private $fileCacheService;

    /**
     * Gets dependency injected FileCacheService
     * @return FileCacheService
     * @throws \Exception
     */
    public function getFileCacheService() : FileCacheService
    {
        if (! $this->fileCacheService) {
            $this->fileCacheService = new FileCacheService(
                Uuid::getFactory(),
                new Filesystem(new LocalFilesystemAdapter(
                    Craft::getAlias('@storage'),
                    LOCK_EX,
                    LocalFilesystemAdapter::DISALLOW_LINKS,
                    [
                        'file' => [
                            'public' => 0777,
                            'private' => 0777,
                        ],
                        'dir' => [
                            'public' => 0777,
                            'private' => 0777,
                        ]
                    ]
                ))
            );
        }

        return $this->fileCacheService;
    }

    /**
     * Gets dependency injected FieldImageProcessService
     * @return FieldImageProcessService
     * @throws \Exception
     */
    public function getFieldImageProcessService() : FieldImageProcessService
    {
        return new FieldImageProcessService(
            $this->getFileCacheService(),
            new ImageManipulator()
        );
    }
}
