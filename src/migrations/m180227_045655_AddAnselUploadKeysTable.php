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
 * m180227_045655_AddAnselUploadKeysTable migration.
 */
class m180227_045655_AddAnselUploadKeysTable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        if ($this->getDb()->tableExists('{{%anselUploadKeys}}')) {
            return true;
        }

        $this->createTable('{{%anselUploadKeys}}', [
            'id' => $this->primaryKey(),
            'key' => $this->text()->notNull(),
            'expires' => $this->integer()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        $this->dropTableIfExists('{{%anselUploadKeys}}');

        return true;
    }
}
