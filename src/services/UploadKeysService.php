<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use craft\db\Query;
use Ramsey\Uuid\Uuid;
use craft\db\Connection;

/**
 * Class UploadKeysService
 */
class UploadKeysService
{
    /** @var Query $query */
    private $query;

    /** @var Connection $dbConnection */
    private $dbConnection;

    /**
     * AnselSettingsService constructor
     * @param Query $query
     * @param Connection $dbConnection
     * @throws \Exception
     */
    public function __construct(
        Query $query,
        Connection $dbConnection
    ) {
        $this->query = $query;
        $this->dbConnection = $dbConnection;

        // Delete expired keys from the database
        $this->dbConnection->createCommand()
            ->delete(
                '{{%anselUploadKeys}}',
                'expires < :time',
                [':time' => time()]
            )->execute();
    }

    /**
     * Creates a new upload key
     * @return string
     * @throws \Exception
     */
    public function createNew() : string
    {
        $uuid = Uuid::uuid4()->toString();

        $this->dbConnection->createCommand()
            ->insert(
                '{{%anselUploadKeys}}',
                [
                    'key' => $uuid,
                    'expires' => strtotime('+ 2 hours')
                ]
            )
            ->execute();

        return $uuid;
    }

    /**
     * Validates a key
     * @param string $key
     * @return bool
     */
    public function isValidKey(string $key) : bool
    {
        $count = (int) (clone $this->query)
            ->from('{{%anselUploadKeys}}')
            ->where('`key` = :key', [':key' => $key])
            ->count();

        return $count > 0;
    }
}
