<?php

declare(strict_types=1);

use lucidtaz\yii2whoops\ErrorHandler;

class CustomErrorHandler extends ErrorHandler
{
    /**
     * If this isn't here, Yii gets cranky
     *
     * @phpstan-ignore-next-line
     */
    public $errorAction;
}
