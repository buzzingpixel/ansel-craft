<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        return $this->iterateAndRun('safeUp');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        return $this->iterateAndRun('safeDown');
    }

    /**
     * @param $method
     * @return bool
     */
    private function iterateAndRun($method) : bool
    {
        /**
         * So apparently in some environments we can't count on the file order
         * from DirectoryIterator to be right
         */

        // So we're going to create an array of classes. The key and the value
        // will be the same so we can ksort() it and have the order be right
        $classes = [];

        foreach (new \DirectoryIterator(__DIR__) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $fileName = $fileInfo->getBasename('.php');

            if ($fileName === 'Install') {
                continue;
            }

            $class = '\\buzzingpixel\\ansel\\migrations\\';
            $class .= $fileInfo->getBasename('.php');

            $classes[$class] = $class;
        }

        // Here's the ksort()
        ksort($classes);

        // Also, if the method is 'safeDown' we should reverse the order
        if ($method === 'safeDown') {
            $classes = array_reverse($classes);
        }

        // Now that have a nice and pretty order we can run those migrations
        foreach ($classes as $class) {
            if (! (new $class())->{$method}()) {
                return false;
            }
        }

        return true;
    }
}
