<?php

namespace buzzingpixel\ansel\migrations;

use Craft;
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
