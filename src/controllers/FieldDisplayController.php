<?php

namespace buzzingpixel\ansel\controllers;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use buzzingpixel\ansel\Ansel;
use buzzingpixel\ansel\models\AnselFieldSettingsModel;

/**
 * Class FieldDisplayController
 */
class FieldDisplayController extends Controller
{
    /**
     * Displays the field
     * @param AnselFieldSettingsModel $settings
     * @return string
     * @throws \Exception
     */
    public function display(AnselFieldSettingsModel $settings) : string
    {
        $settings->retinizeValues();

        return \Minify_HTML::minify(
            $this->getView()->renderTemplate('ansel/_field/Index.twig', [
                'uploadKey' => Ansel::$plugin->getUploadKeysService()->createNew(),
                'uploadActionUrl' => UrlHelper::actionUrl('ansel/field-upload/upload'),
                'processActionUrl' => UrlHelper::actionUrl('ansel/image-process/process'),
                'csrfToken' => Craft::$app->getRequest()->getCsrfToken(),
                'settings' => $settings,
            ])
        );
    }
}
