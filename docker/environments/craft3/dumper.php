<?php

declare(strict_types=1);

use Morrislaptop\VarDumperWithContext\CliDumper;
use Morrislaptop\VarDumperWithContext\HtmlDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

$cloner = new VarCloner();

$htmlDumper = new class() extends HtmlDumper
{
    protected function getCaller()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($backtrace as $trace) {
            if (!empty($trace['file']) &&
                !empty($trace['line']) &&
                strpos($trace['file'], '/vendor/') === false &&
                strpos($trace['file'], 'dumper.php') === false
            ) {
                return $trace;
            }
        }

        return [];
    }
};

$htmlDumper->setTheme('light');

$fallbackDumper = in_array(
    PHP_SAPI,
    ['cli', 'phpdbg'],
    true
) ? new CliDumper() : $htmlDumper;

$contextProviders = [
    'cli' => new CliContextProvider(),
    'source' => new SourceContextProvider('UTF-8'),
];

$dumper = new ServerDumper(
    'tcp://127.0.0.1:9912',
    $fallbackDumper,
    $contextProviders
);

VarDumper::setHandler(static function ($var) use ($cloner, $dumper): void {
    $dumper->dump($cloner->cloneVar($var));
});
