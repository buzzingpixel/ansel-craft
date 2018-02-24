<?php

namespace buzzingpixel\ansel\controllers;

use craft\web\Controller;

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
        $this->renderTemplate('ansel/_core/Settings.twig', []);
    }
}
