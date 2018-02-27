<?php

namespace buzzingpixel\ansel\controllers;

use buzzingpixel\ansel\Ansel;
use craft\web\Controller;
use buzzingpixel\ansel\services\UploadKeysService;

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
        ]);
    }
}
