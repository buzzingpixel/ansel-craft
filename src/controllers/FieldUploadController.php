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
        $this->requirePostRequest();

        $requestService = Craft::$app->getRequest();

        $validKey = Ansel::$plugin->getUploadKeysService()->isValidKey(
            $requestService->post('uploadKey', '')
        );

        if (! $validKey) {
            return $this->asJson([
                'success' => false,
                'message' => 'Invalid upload key',
                'file' => [],
            ]);
        }

        $cacheService = Ansel::$plugin->getFileCacheService();

        $uuid = Uuid::uuid4()->toString();
        $cachePath = $cacheService->getCachePath() .
            "/{$uuid}";

        $file = $_FILES['file'] ?? [];
        $file = \is_array($file) ? $file : [];

        $tmpFile = $file['tmp_name'] ?? null;

        if (! file_exists($tmpFile)) {
            return $this->asJson([
                'success' => false,
                'message' => 'The uploaded file could not be found. The most common reason for this is you tried to upload a file that is larger than your server allows.',
            ]);
        }

        $file = new Image($file);
        $file->setDimension(999999999, 999999999);
        $file->setMime(['gif', 'jpeg', 'png']);
        $file->setLocation($cachePath, 0777);
        $file->setSize(0, 999999999999999999999);

        $minWidth = (int) $requestService->post('minWidth');
        $minHeight = (int) $requestService->post('minHeight');

        try {
            $meetsMin = true;

            if ($minWidth && $file->getWidth() < $minWidth) {
                $meetsMin = false;
            }

            if ($minHeight && $file->getHeight() < $minHeight) {
                $meetsMin = false;
            }

            if (! $meetsMin) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Minimum dimensions not met.',
                    'file' => [],
                ]);
            }
        } catch (\Exception $e) {
            if ($e->getMessage() === 'getimagesize(): Filename cannot be empty') {
                return $this->asJson([
                    'success' => false,
                    'message' => 'The uploaded file could not be found. The most common reason for this is you tried to upload a file that is larger than your server allows.',
                ]);
            }

            return $this->asJson([
                'success' => false,
                'message' => 'An unknown error occurred.',
                'file' => [],
            ]);
        }

        try {
            $success = $file->upload();
        } catch (\Exception $e) {
            return $this->asJson([
                'success' => false,
                'message' => 'An unknown error occurred.',
                'file' => [],
            ]);
        }

        if (! $success) {
            return $this->asJson([
                'success' => false,
                'message' => $file->getError(),
                'file' => [],
            ]);
        }

        $cacheFile = "{$uuid}/{$file->getName()}.{$file->getMime()}";
        $base64 = "data:image/{$file->getMime()};base64,";
        $base64 .= base64_encode($cacheService->getCacheFileContents(
            $cacheFile
        ));

        return $this->asJson([
            'success' => true,
            'message' => null,
            'file' => [
                'cacheFile' => $cacheFile,
                'base64' => $base64,
            ],
        ]);
    }
}
