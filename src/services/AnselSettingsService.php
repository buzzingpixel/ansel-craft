<?php

namespace buzzingpixel\ansel\services;

use buzzingpixel\ansel\models\AnselSettingsModel;
use craft\db\Query;

/**
 * Class AnselSettingsService
 */
class AnselSettingsService
{
    /** @var Query $query */
    private $query;

    /**
     * AnselSettingsService constructor
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
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
}
