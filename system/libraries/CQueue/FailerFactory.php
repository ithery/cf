<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 5, 2019, 5:35:15 PM
 */
use Aws\DynamoDb\DynamoDbClient;

class CQueue_FailerFactory {
    private static $failerInstance;

    public static function getFailerInstance() {
        $config = CQueue::config('failed');
        if (isset($config['driver']) && $config['driver'] === 'dynamodb') {
            return static::dynamoFailedJobProvider($config);
        } elseif (isset($config['table'])) {
            return static::databaseFailedJobProvider($config);
        } else {
            return new CQueue_FailedJob_NullFailedJob();
        }
    }

    /**
     * Create a new database failed job provider.
     *
     * @param array $config
     *
     * @return \CQueue_FailedJob_DatabaseFailedJob
     */
    protected static function databaseFailedJobProvider($config) {
        $db = c::db(carr::get($config, 'database'));

        return new CQueue_FailedJob_DatabaseFailedJob(
            $db,
            carr::get($config, 'table')
        );
    }

    /**
     * Create a new DynamoDb failed job provider.
     *
     * @param array $config
     *
     * @return \CQueue_FailedJob_DynamoDbFailedJob
     */
    protected static function dynamoFailedJobProvider($config) {
        $dynamoConfig = [
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => carr::get($config, 'endpoint'),
        ];
        if (!empty($config['key']) && !empty($config['secret'])) {
            $dynamoConfig['credentials'] = carr::only($config, ['key', 'secret', 'token']);
        }

        return new CQueue_FailedJob_DynamoDbFailedJob(
            new DynamoDbClient($dynamoConfig),
            CF::appCode(),
            $config['table']
        );
    }
}
