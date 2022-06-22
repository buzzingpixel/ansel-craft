<?php

declare(strict_types=1);

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

$cloner = new VarCloner();

$htmlDumper = new HtmlDumper();

$htmlDumper->setTheme('light');

$isCli = in_array(PHP_SAPI, ['cli', 'phpdbg'], true);

$fallbackDumper = $isCli ? new CliDumper() : $htmlDumper;

$dumper = new ServerDumper(
    'tcp://127.0.0.1:9912',
    $fallbackDumper,
    [
        'cli' => new CliContextProvider(),
        'source' => new SourceContextProvider(),
    ]
);

$varStore            = new stdClass();

$varStore->hasDumped = false;

VarDumper::setHandler(static function ($var) use (
    $cloner,
    $dumper,
    $varStore
): void {
    $dumper->dump($cloner->cloneVar($var));

    return;

    $traceStack = [];

    $passedDump = false;

    foreach (debug_backtrace() as $traceItem) {
        $class = $traceItem['class'] ?? '';

        if ($class === VarDumper::class) {
            $passedDump = true;

            continue;
        }

        if (! $passedDump) {
            continue;
        }

        $traceStack[] = $traceItem;
    }

    /**
     * @psalm-suppress RedundantCondition
     * @phpstan-ignore-next-line
     */
    if (PHP_SAPI !== 'cli' && $varStore->hasDumped === false) {
        echo '<head><title>Symfony Dumper</title></head><body>';
        $varStore->hasDumped = true;
    }

    /** @psalm-suppress MixedAssignment */
    $checkForTwigDumperFile = $traceStack[1]['file'] ?? '';

    if (! $checkForTwigDumperFile) {
        /** @psalm-suppress MixedAssignment */
        $checkForTwigDumperFile = $traceStack[2]['file'] ?? '';
    }

    /** @psalm-suppress MixedArgument */
    $checkForTwigDumperArray = explode(
        DIRECTORY_SEPARATOR,
        $checkForTwigDumperFile
    );

    $isTwigDumper = $checkForTwigDumperArray[count($checkForTwigDumperArray) - 1] === 'TwigDumper.php';

    if ($isTwigDumper) {
        echo '<div></div>';
        echo '<div style="background-color: #fff; display: inline-block; margin: 10px; padding: 25px;">';
        echo '<pre style="font-size: 14px; line-height: 40px; margin-bottom: -10px; margin-left: 6px; background-color: #fff;">';
        if (is_object($var)) {
            echo get_class($var);
        } else {
            echo gettype($var);
        }

        echo '</pre>';
        $dumper->dump($cloner->cloneVar($var));
        echo '</div><br>';

        return;
    }

    $traceItem = $traceStack[0];

    if (PHP_SAPI !== 'cli') {
        echo '<pre style="margin-bottom: -16px; background-color: #fff">';
    }

    /** @psalm-suppress MixedOperand */
    echo $traceItem['file'] . ':' . $traceItem['line'] . ': ';

    if (PHP_SAPI !== 'cli') {
        echo '</pre>';
        echo '<pre style="font-size: 14px; margin-bottom: -16px; margin-left: 6px; background-color: #fff">';
    }

    if (is_object($var)) {
        echo get_class($var) . ' ';
    } else {
        echo gettype($var) . ' ';
    }

    if (PHP_SAPI !== 'cli') {
        echo '</pre>';
    }

    $dumper->dump($cloner->cloneVar($var));
});
