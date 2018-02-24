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
 * m180224_032635_AddSettingsTable migration.
 */
class m180224_032635_AddSettingsTable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        if ($this->getDb()->tableExists('{{%anselSettings}}')) {
            return true;
        }

        $this->createTable('{{%anselSettings}}', [
            'id' => $this->primaryKey(),
            'settingsType' => $this->tinyText()->notNull(),
            'settingsKey' => $this->tinyText()->notNull(),
            'settingsValue' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->batchInsert(
            '{{%anselSettings}}',
            ['settingsType', 'settingsKey', 'settingsValue'],
            [
                ['string', 'licenseKey', null],
                ['int', 'phoneHome', 0],
                ['string', 'defaultHost', null],
                ['int', 'defaultMaxQty', null],
                ['int', 'defaultImageQuality', 90],
                ['bool', 'defaultJpg', 'n'],
                ['bool', 'defaultRetina', 'n'],
                ['bool', 'defaultShowTitle', 'n'],
                ['bool', 'defaultRequireTitle', 'n'],
                ['string', 'defaultTitleLabel', null],
                ['bool', 'defaultShowCaption', 'n'],
                ['bool', 'defaultRequireCaption', 'n'],
                ['string', 'defaultCaptionLabel', null],
                ['bool', 'defaultShowCover', 'n'],
                ['bool', 'defaultRequireCover', 'n'],
                ['string', 'defaultCoverLabel', null],
                ['bool', 'hideSourceSaveInstructions', 'n'],
                ['string', 'encoding', ''],
                ['string', 'encodingData', ''],
            ]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        $this->dropTableIfExists('{{%anselSettings}}');

        return true;
    }
}
