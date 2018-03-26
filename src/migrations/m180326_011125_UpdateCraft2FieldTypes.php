<?php

namespace buzzingpixel\ansel\migrations;

use craft\db\Migration;
use buzzingpixel\ansel\fields\AnselField;

/**
 * m180326_011125_UpdateCraft2FieldTypes migration.
 */
class m180326_011125_UpdateCraft2FieldTypes extends Migration
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function safeUp() : bool
    {
        $this->getDb()->createCommand()
            ->update(
                '{{%fields}}',
                [
                    'type' => AnselField::class,
                ],
                "`type` = 'Ansel_Ansel'"
            )
            ->execute();

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
