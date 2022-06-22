<?php

declare(strict_types=1);

require_once __DIR__ . '/CustomErrorHandler.php';

$config = [
    'components' => [
        'errorHandler' => [
            'class' => CustomErrorHandler::class,
        ],
    ],
];

return $config;
