<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\controllers;

use Craft;
use yii\web\Response;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use buzzingpixel\ansel\Ansel;
use buzzingpixel\ansel\models\AnselSettingsModel;
use buzzingpixel\ansel\services\AnselSettingsService;

/**
 * Class CpSettingsController
 */
class CpSettingsController extends Controller
{
    /** @var AnselSettingsService $settingsService */
    private $settingsService;

    /** @var AnselSettingsModel $settings */
    private $settings;

    /**
     * Runs in controller init
     * @throws \Exception
     */
    public function init()
    {
        $this->settingsService = Ansel::$plugin->getAnselSettingsService();
        $this->settings = $this->settingsService->getSettings();
    }

    /**
     * Displays the settings page
     * @return Response
     * @throws \Exception
     */
    public function actionIndex() : Response
    {
        return $this->renderTemplate('ansel/_core/Settings.twig', [
            'settings' => $this->settings,
        ]);
    }

    /**
     * Saves the settings
     * @return Response
     * @throws \Exception
     */
    public function actionSaveSettings() : Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        foreach (array_keys($this->settings->asArray(true)) as $key) {
            $this->settings->setProperty($key, $request->post($key));
        }

        if (! $this->settings->validate()) {
            if (Craft::$app->getSession()) {
                Craft::$app->getSession()->setError(
                    Craft::t('ansel', "Couldn't save settings.")
                );
            }
            return $this->actionIndex();
        }

        $this->settingsService->saveSettings($this->settings);

        if (Craft::$app->getSession()) {
            Craft::$app->getSession()->setNotice(
                Craft::t('ansel', 'Settings saved.')
            );
        }

        return $this->redirect(UrlHelper::cpUrl('ansel'));
    }
}
