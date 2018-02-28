<?php

namespace buzzingpixel\ansel\controllers;

use Craft;
use yii\web\Response;
use Ramsey\Uuid\Uuid;
use BulletProof\Image;
use craft\web\Controller;
use buzzingpixel\ansel\Ansel;

/**
 * Class FieldUploadController
 */
class FieldUploadController extends Controller
{
    /** @var bool $allowAnonymous */
    protected $allowAnonymous = true;

    /**
     * Handles file uploads
     * @return Response
     * @throws \Exception
     */
    public function actionUpload() : Response
    {
        ini_set('xdebug.overload_var_dump', 'off');

        $this->requirePostRequest();

        $validKey = Ansel::$plugin->getUploadKeysService()->isValidKey(
            Craft::$app->getRequest()->post('uploadKey')
        );

        if (! $validKey) {
            return $this->asJson([
                'success' => false,
                'message' => 'Invalid upload key',
            ]);
        }

        $uuid = Uuid::uuid4()->toString();

        $file = $_FILES['file'] ?? [];
        $file = \is_array($file) ? $file : [];
        $file = new Image($file);
        $file->setDimension(999999999, 999999999);
        $file->setMime(['gif', 'jpeg', 'png']);
        $file->setLocation(
            Ansel::$plugin->getFileCacheService()->getCachePath() . "/{$uuid}",
            0777
        );

        // TODO: validate constraints
        // $file->getWidth();
        // $file->getHeight();

        var_dump('here', $file->upload());
        die;

        var_dump($file);
        die;
    }
}
