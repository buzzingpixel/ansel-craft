<?php

namespace buzzingpixel\ansel\services;

use craft\db\Query;
use craft\db\Connection;
use yii\web\ServerErrorHttpException;
use buzzingpixel\ansel\models\AnselSettingsModel;

/**
 * Class AnselSettingsService
 */
class AnselSettingsService
{
    /** @var Query $query */
    private $query;

    /** @var Connection $dbConnection */
    private $dbConnection;

    /**
     * AnselSettingsService constructor
     * @param Query $query
     * @param Connection $dbConnection
     */
    public function __construct(Query $query, Connection $dbConnection)
    {
        $this->query = $query;
        $this->dbConnection = $dbConnection;
    }

    /**
     * Gets Ansel's settings
     * @return AnselSettingsModel
     * @throws \ReflectionException
     */
    public function getSettings() : AnselSettingsModel
    {
        $properties = [];

        $settings = (clone $this->query)->from('{{%anselSettings}}')->all();

        foreach ($settings as $setting) {
            $properties[$setting['settingsKey']] = $setting['settingsValue'];
        }

        return new AnselSettingsModel($properties);
    }

    /**
     * Saves Ansel's settings
     * @param AnselSettingsModel $settings
     * @throws \Exception
     */
    public function saveSettings(AnselSettingsModel $settings)
    {
        if (! $settings->validate()) {
            throw new ServerErrorHttpException('Settings do not validate');
        }

        foreach ($settings->asArray(true) as $key => $val) {
            $this->dbConnection->createCommand()
                ->update(
                    '{{%anselSettings}}',
                    ['settingsValue' => $val],
                    "settingsKey = '{$key}'"
                )
                ->execute();
        }
    }
}
