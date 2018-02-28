<?php

namespace buzzingpixel\ansel\controllers;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use buzzingpixel\ansel\Ansel;

/**
 * Class FieldDisplayController
 */
class FieldDisplayController extends Controller
{
    /**
     * Displays the field
     * @return string
     * @throws \Exception
     */
    public function display() : string
    {
        return $this->getView()->renderTemplate('ansel/_field/Index.twig', [
            'uploadKey' => Ansel::$plugin->getUploadKeysService()->createNew(),
            'uploadActionUrl' => UrlHelper::actionUrl('ansel/field-upload/upload'),
            'csrfToken' => Craft::$app->getRequest()->getCsrfToken()
        ]);
    }
}
