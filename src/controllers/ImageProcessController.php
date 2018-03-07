<?php

namespace buzzingpixel\ansel\controllers;

use Craft;
use yii\web\Response;
use craft\web\Controller;
use buzzingpixel\ansel\Ansel;

/**
 * Class ImageProcessController
 */
class ImageProcessController extends Controller
{
    /** @var bool $allowAnonymous */
    protected $allowAnonymous = true;

    /**
     * Handles pre-processing of images
     * @return Response
     * @throws \Exception
     */
    public function actionProcess() : Response
    {
        $this->requirePostRequest();

        $requestService = Craft::$app->getRequest();

        $validKey = Ansel::$plugin->getUploadKeysService()->isValidKey(
            $requestService->post('uploadKey', '')
        );

        if (! $validKey) {
            return $this->asJson([
                'success' => false,
                'message' => 'Invalid upload key',
            ]);
        }

        var_dump($requestService->post('h'));
        var_dump($requestService->post('w'));
        var_dump($requestService->post('x'));
        var_dump($requestService->post('y'));
        var_dump($requestService->post('fileLocation'));
        var_dump($requestService->post('fileLocationType'));
        die;
    }
}
