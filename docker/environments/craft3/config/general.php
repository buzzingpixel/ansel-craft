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
    'appId' => 'ansel-craft',
    'backupOnUpdate' => false,
    'basePath' => $craftBasePath,
    'cpTrigger' => 'admin',
    'devMode' => true,
    'generateTransformsBeforePageLoad' => true,
    'isSystemLive' => true,
    'maxUploadFileSize' => 512000000,
    'omitScriptNameInUrls' => true,
    'postCpLoginRedirect' => 'entries',
    'projectPath' => $craftBasePath,
    'runQueueAutomatically' => true,
    'securityKey' => 'jZUaYGnKB7zSmS2BvVtfjCFKE4JthrsK',
    'sendPoweredByHeader' => false,
    'timezone' => 'America/Chicago',
    'useEmailAsUsername' => true,
    'useProjectConfigFile' => true,
    'enableTemplateCaching' => false,
    'addTrailingSlashesToUrls' => false,

    'requireMatchingUserAgentForSession' => false,
    'rememberedUserSessionDuration' => 'P1Y',
    'userSessionDuration' => 'P1Y',
    'phpSessionName' => 'PHPSESSID',
    'autosaveDrafts' => false,

    'aliases' => [
        '@webroot' => $craftBasePath . '/public',
        '@baseurl' => $baseUrl,
    ],
];
