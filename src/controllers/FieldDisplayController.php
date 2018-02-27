<?php

namespace buzzingpixel\ansel\controllers;

use craft\web\Controller;

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
        return $this->getView()->renderTemplate('ansel/_field/Index.twig', []);
    }
}
