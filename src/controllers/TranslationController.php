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

/**
 * Class ImageProcessController
 */
class TranslationController extends Controller
{
    /** @var bool $allowAnonymous */
    protected $allowAnonymous = true;

    /**
     * @return Response
     * @throws \Exception
     */
    public function actionTranslate() : Response
    {
        $this->requirePostRequest();

        $requestService = Craft::$app->getRequest();

        $toTranslate = $requestService->post('toTranslate');

        $items = [];

        foreach ($toTranslate as $key => $item) {
            $items[$key] = Craft::t('app', $item);
        }

        return $this->asJson($items);
    }
}
