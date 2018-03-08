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
 * m180308_144534_AddAnselImagesTable migration.
 */
class m180308_144534_AddAnselImagesTable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        if ($this->getDb()->tableExists('{{%anselImages}}')) {
            return true;
        }

        $this->createTable('{{%anselImages}}', [
            'id' => $this->primaryKey(),
            'elementId' => $this->integer(11)->notNull(),
            'fieldId' => $this->integer(11)->notNull(),
            'userId' => $this->integer(11)->notNull(),
            'assetId' => $this->integer(11),
            'highQualAssetId' => $this->integer(11),
            'thumbAssetId' => $this->integer(11),
            'originalAssetId' => $this->integer(11),
            'width' => $this->integer()->notNull(),
            'height' => $this->integer()->notNull(),
            'x' => $this->integer()->notNull(),
            'y' => $this->integer()->notNull(),
            'title' => $this->string(255),
            'caption' => $this->string(255),
            'cover' => $this->tinyInteger(1)->notNull(),
            'position' => $this->tinyInteger(4)->notNull(),
            'disabled' => $this->tinyInteger(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            ['elementId'],
            '{{%elements}}',
            ['id'],
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            ['fieldId'],
            '{{%fields}}',
            ['id'],
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            'userId',
            '{{%users}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            ['assetId'],
            '{{%assets}}',
            ['id'],
            'SET NULL',
            'RESTRICT'
        );

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            ['highQualAssetId'],
            '{{%assets}}',
            ['id'],
            'SET NULL',
            'RESTRICT'
        );

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            ['thumbAssetId'],
            '{{%assets}}',
            ['id'],
            'SET NULL',
            'RESTRICT'
        );

        $this->addForeignKey(
            null,
            '{{%anselImages}}',
            ['originalAssetId'],
            '{{%assets}}',
            ['id'],
            'SET NULL',
            'RESTRICT'
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown() : bool
    {
        $this->dropTableIfExists('{{%anselImages}}');

        return true;
    }
}
