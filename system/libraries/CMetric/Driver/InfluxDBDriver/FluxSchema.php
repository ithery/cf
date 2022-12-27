<?php
use InfluxDB2\Client;

class CMetric_Driver_InfluxDBDriver_FluxSchema {
    public static function getFieldKeys(Client $client, $measurement = null, $bucket = null) {
        $measurementParam = '';
        $method = 'fieldKeys';
        if ($measurement != null) {
            $measurementParam = 'measurement: "' . $measurement . '",';
            $method = 'measurementFieldKeys';
        }

        return static::getResult($client, $bucket, $method, $measurementParam);
    }

    public static function getTagKeys(Client $client, $measurement = null, $bucket = null) {
        $measurementParam = '';
        $method = 'tagKeys';
        if ($measurement != null) {
            $measurementParam = 'measurement: "' . $measurement . '",';
            $method = 'measurementTagKeys';
        }

        return static::getResult($client, $bucket, $method, $measurementParam);
    }

    private static function getResult(Client $client, $bucket, $method, $measurementParam) {
        $result = $client->createQueryApi()->query('import "influxdata/influxdb/schema"' . PHP_EOL . PHP_EOL . 'schema.' . $method . '(bucket: "' . $bucket . '", ' . $measurementParam . ')');
        $first = carr::first($result);
        $results = [];
        foreach ($first->records as $record) {
            $results[] = $record->values['_value'];
        }

        return $results;
    }

    public static function getVersion(Client $client) {
        $result = $client->createQueryApi()->query('import "array"' . PHP_EOL . 'import "runtime"' . PHP_EOL . PHP_EOL . 'array.from(rows: [{version: runtime.version()}])');
        $first = carr::first($result);

        return $first->records[0]->values['version'];
    }
}
