<?php

declare(strict_types=1);

// Start session
session_start();

/**
 * CraftCMS front controller
 */

use Symfony\Component\VarDumper\VarDumper;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use yii\base\Application;

const CRAFT_BASE_PATH = __DIR__;

const APP_ROOT = __DIR__;

const CRAFT_VENDOR_PATH = __DIR__ . '/vendor';

/** @psalm-suppress UnresolvableInclude */
require_once CRAFT_VENDOR_PATH . '/autoload.php';

if (class_exists(VarDumper::class)) {
    /** @psalm-suppress UnresolvableInclude */
    require APP_ROOT . '/dumper.php';
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     dd($_POST);
// }

if (PHP_SAPI === 'cli') {
    // Register handler to catch errors that come up before Yii registers a handler
    $whoops = new Run();
    $whoops->pushHandler(new PlainTextHandler());
    $whoops->register();

    /** @psalm-suppress UnresolvableInclude */
    $app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/console.php';
    assert($app instanceof Application);
    $exitCode = $app->run();
    exit($exitCode);
}

// Register handler to catch errors that come up before Yii registers a handler
$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

/**
 * @psalm-suppress MixedAssignment
 * @psalm-suppress UnresolvableInclude
 */
$app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/web.php';
assert($app instanceof Application);
$app->run();
