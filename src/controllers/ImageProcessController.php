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
use buzzingpixel\ansel\Ansel;
use buzzingpixel\ansel\models\ProcessedFieldImageModel;

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

        $processedFieldImageModel = new ProcessedFieldImageModel([
            'h' => $requestService->post('h'),
            'w' => $requestService->post('w'),
            'x' => $requestService->post('x'),
            'y' => $requestService->post('y'),
            'fileLocation' => $requestService->post('fileLocation'),
            'fileLocationType' => $requestService->post('fileLocationType'),
            'quality' => $requestService->post('quality'),
            'maxWidth' => $requestService->post('maxWidth'),
            'maxHeight' => $requestService->post('maxHeight'),
            'forceJpg' => $requestService->post('forceJpg'),
        ]);

        Ansel::$plugin->getFieldImageProcessService()->processImage(
            $processedFieldImageModel
        );

        return $this->asJson([
            'success' => true,
            'model' => $processedFieldImageModel->asArray(true)
        ]);
    }
}
