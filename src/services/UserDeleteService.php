<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use craft\elements\User;
use craft\db\Connection as DbConnection;

/**
 * Class UserDeleteService
 */
class UserDeleteService
{
    /** @var DbConnection $dbConnection */
    private $dbConnection;

    /**
     * UserDeleteService constructor
     * @param DbConnection $dbConnection
     */
    public function __construct(DbConnection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Responds to user deletion
     * @param User $user
     * @throws \Exception
     */
    public function onDeleteUser(User $user)
    {
        $userId = (int) $user->getId();
        $newUserId = null;

        if ($user->inheritorOnDelete) {
            $newUserId = (int) $user->inheritorOnDelete->getId();
        }

        if (! $newUserId) {
            $userQuery = User::find()->where('`admin` = 1')
                ->andWhere("`users`.`id` != {$userId}")
                ->one();

            $newUserId = (int) $userQuery->getId();
        }

        $this->dbConnection->createCommand()->update(
            '{{%anselImages}}',
            [
                'userId' => $newUserId,
            ],
            "`userId` = {$userId}"
        )
        ->execute();
    }
}
