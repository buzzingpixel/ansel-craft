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
 * m180224_055227_RemoveOldLicensingSettings migration.
 */
class m180224_055227_RemoveOldLicensingSettings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        $this->delete('{{%anselSettings}}', "settingsKey = 'licenseKey'");
        $this->delete('{{%anselSettings}}', "settingsKey = 'phoneHome'");
        $this->delete('{{%anselSettings}}', "settingsKey = 'encoding'");
        $this->delete('{{%anselSettings}}', "settingsKey = 'encodingData'");

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        return true;
    }
}
