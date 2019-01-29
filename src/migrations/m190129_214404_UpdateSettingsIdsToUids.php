<?php

namespace buzzingpixel\ansel\migrations;

use Craft;
use craft\db\Query;
use craft\db\Migration;
use buzzingpixel\ansel\fields\AnselField;

/**
 * m190129_214404_UpdateSettingsIdsToUids migration.
 */
class m190129_214404_UpdateSettingsIdsToUids extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $class = explode('\\', AnselField::class);

        $classString = '';

        foreach ($class as $part) {
            if (! $part) {
                continue;
            }

            if ($classString) {
                $classString .= '\\\\';
            }

            $classString .= $part;
        }

        $fields = (new Query())->from('{{%fields}}')
            ->where("`type` = '" . $classString . "'")
            ->all();

        foreach ($fields as $field) {
            if (! $json = json_decode($field['settings'], true)) {
                continue;
            }

            $this->reSaveFieldSettings(json_decode($field['settings'], true), $field['id']);
        }
    }

    /**
     * @param array $settings
     * @param $primaryKey
     */
    private function reSaveFieldSettings(array $settings, $primaryKey)
    {
        if (isset($settings['uploadLocation']) && is_numeric($settings['uploadLocation'])) {
            $volume = Craft::$app->getVolumes()->getVolumeById((int) $settings['uploadLocation']);

            if ($volume && isset($volume->uid)) {
                $settings['uploadLocation'] = $volume->uid;
            }
        }

        if (isset($settings['saveLocation']) && is_numeric($settings['saveLocation'])) {
            $volume = Craft::$app->getVolumes()->getVolumeById((int) $settings['saveLocation']);

            if ($volume && isset($volume->uid)) {
                $settings['saveLocation'] = $volume->uid;
            }
        }

        $this->getDb()->createCommand()->update(
            '{{%fields}}',
            [
                'settings' => json_encode($settings),
            ],
            "`id` = '" . $primaryKey . "'"
        )
        ->execute();
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        return true;
    }
}
