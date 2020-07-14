<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 5, 2019, 5:35:15 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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
            return new CQueue_FailedJob_NullFailedJob;
        }
    }

    /**
     * Create a new database failed job provider.
     *
     * @param  array  $config
     * @return \Illuminate\Queue\Failed\DatabaseFailedJobProvider
     */
    protected static function databaseFailedJobProvider($config) {

        $db = CDatabase::instance(carr::get($config, 'database'));
        return new CQueue_FailedJob_DatabaseFailedJob(
                $db, carr::get($config, 'table')
        );
    }

    /**
     * Create a new DynamoDb failed job provider.
     *
     * @param  array  $config
     * @return \Illuminate\Queue\Failed\DynamoDbFailedJobProvider
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
                new DynamoDbClient($dynamoConfig), CF::appCode(), $config['table']
        );
    }

}
