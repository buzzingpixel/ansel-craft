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
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\services\Fields;
use \craft\helpers\UrlHelper;
use craft\events\RegisterUrlRulesEvent;
use buzzingpixel\ansel\fields\AnselField;
use craft\events\RegisterComponentTypesEvent;
use buzzingpixel\ansel\services\AnselSettingsService;

/**
 * Class Ansel
 */
class Ansel extends Plugin
{
    /** @var Ansel $plugin */
    public static $plugin;

    /**
     * Initialize plugin
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

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
}
