<?php

namespace buzzingpixel\ansel\controllers;

use craft\web\Controller;
use buzzingpixel\ansel\Ansel;

/**
 * Class CpSettingsController
 */
class CpSettingsController extends Controller
{
    /**
     * Displays the settings index
     * @throws \Exception
     */
    public function actionIndex()
    {
        $this->renderTemplate('ansel/_core/Settings.twig', [
            'settings' => Ansel::$plugin->getAnselSettingsService()->getSettings(),
        ]);
    }
}
