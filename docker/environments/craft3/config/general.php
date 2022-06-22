<?php

declare(strict_types=1);

/** @phpstan-ignore-next-line */
$craftBasePath = (string) CRAFT_BASE_PATH;

$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
    );

$protocol = $secure ? 'https://' : 'http://';

$baseUrl = $protocol . ((string) $_SERVER['HTTP_HOST']);

return [
    'allowAdminChanges' => true,
    'resourceBaseUrl' => $baseUrl . '/cpresources',
    'baseCpUrl' => $baseUrl,
    'allowUpdates' => false,
    'appId' => 'ansel',
    'backupOnUpdate' => false,
    'cacheDuration' => 0,
    'cacheMethod' => 'apc',
    'basePath' => $craftBasePath,
    'cpTrigger' => 'admin',
    'devMode' => true,
    'generateTransformsBeforePageLoad' => true,
    'isSystemLive' => true,
    'maxUploadFileSize' => 512000000,
    'omitScriptNameInUrls' => true,
    'postCpLoginRedirect' => 'entries',
    'projectPath' => $craftBasePath,
    'rememberedUserSessionDuration' => 'P100Y', // 100 years
    'runQueueAutomatically' => true,
    'securityKey' => 'jZUaYGnKB7zSmS2BvVtfjCFKE4JthrsK',
    'sendPoweredByHeader' => false,
    'timezone' => 'America/Chicago',
    'useEmailAsUsername' => true,
    'useProjectConfigFile' => true,
    'userSessionDuration' => false, // As long as browser stays open
    'staticAssetCacheTime' => '',
    'enableTemplateCaching' => false,
    'addTrailingSlashesToUrls' => false,

    'aliases' => [
        '@webroot' => $craftBasePath . '/public',
        '@baseurl' => $baseUrl,
    ],
];
