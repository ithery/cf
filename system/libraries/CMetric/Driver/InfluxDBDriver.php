<?php
use InfluxDB2\Point;
use InfluxDB2\Client;
use InfluxDB2\WriteApi;
use InfluxDB2\WriteType;
use InfluxDB2\Model\WritePrecision;

/**
 * Class CMetric_Driver_InfluxDB.
 */
class CMetric_Driver_InfluxDBDriver extends CMetric_DriverAbstract {
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $precision;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * InfluxDB constructor.
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->bucket = carr::get($options, 'bucket');
        $this->client = new Client(carr::only($options, ['url', 'token', 'bucket', 'org', 'precision']));
        $this->precision = carr::get($options, 'precision', WritePrecision::NS);
    }

    /**
     * A public way tog et the nanosecond precision we desire.
     *
     * @param mixed $timestamp
     *
     * @return null|int
     */
    public function getNanoTimestamp($timestamp = null) {
        if ($timestamp instanceof \DateTime) {
            return $timestamp->format('Uu') * 1000;
        }

        // We weren't given a valid timestamp, generate.
        return c::now()->format('Uu') * 1000;
    }

    /**
     * @throws \InfluxDB2\ApiException
     *
     * @return $this
     */
    public function flush() {
        if (empty($this->getMetrics())) {
            return $this;
        }

        $this->send($this->getMetrics());
        $this->metrics = [];

        return $this;
    }

    /**
     * @param Metric $metric
     *
     * @throws \InfluxDB2\Database\Exception
     *
     * @return Point
     */
    public function format(CMetric_Metric $metric) {
        $fields = array_merge($this->extra, $metric->getExtra());
        if ($value = $metric->getValue()) {
            $fields['value'] = $value;
        }

        return new Point(
            $metric->getName(),
            array_merge($this->tags, $metric->getTags()),
            $fields,
            $this->getNanoTimestamp($metric->getTimestamp()),
            $this->precision
        );
    }

    /**
     * Send one or more metrics to InfluxDB now.
     *
     * @param $metrics
     *
     * @throws \InfluxDB2\ApiException
     */
    public function send($metrics) {
        // $resp = $this->client->createQueryApi()->query('
        // from(bucket: "testing")
        // |> range(start: -8h)
        // |> filter(fn: (r) => r["_measurement"] == "devcloud_home")
        // ');

        // cdbg::dd($resp);

        $writer = $this->createWriter(count($metrics));
        foreach ($metrics as $point) {
            if ($point instanceof CMetric_Metric) {
                $point = $this->format($point);
            }
            $writer->write($point);
        }

        $writer->close();
    }

    private function createWriter(int $batchSize): WriteApi {
        return $this->client->createWriteApi(
            [
                'writeType' => WriteType::BATCHING,
                'batchSize' => $batchSize,
            ]
        );
    }

    /**
     * @return array
     */
    public function getPoints() {
        return $this->points;
    }

    /**
     * @param CMetric_QueryBuilder $query
     *
     * @return string
     */
    public function formatQuery(CMetric_QueryBuilder $query) {
        if ($query->getFrom() == null) {
            $query->setFrom($this->bucket);
        }

        return (new CMetric_Driver_InfluxDBDriver_FluxFormatter($query))->toFluxString();
    }

    public function getBuckets() {
        $result = $this->client->createQueryApi()->query('buckets()');
        $first = carr::first($result);
        $buckets = [];
        foreach ($first->records as $record) {
            $buckets[] = $record->values['name'];
        }

        return $buckets;
    }

    public function getMeasurements($bucket = null) {
        if ($bucket == null) {
            $bucket = $this->bucket;
        }
        $result = $this->client->createQueryApi()->query('import "influxdata/influxdb/schema"' . PHP_EOL . PHP_EOL . 'schema.measurements(bucket: "' . $bucket . '")');
        $first = carr::first($result);
        $measurements = [];
        foreach ($first->records as $record) {
            $measurements[] = $record->values['_value'];
        }

        return $measurements;
    }

    public function getFieldKeys($measurement = null, $bucket = null) {
        if ($bucket == null) {
            $bucket = $this->bucket;
        }

        return CMetric_Driver_InfluxDBDriver_FluxSchema::getFieldKeys($this->client, $measurement, $bucket);
    }

    public function getTagKeys($measurement = null, $bucket = null) {
        if ($bucket == null) {
            $bucket = $this->bucket;
        }

        return CMetric_Driver_InfluxDBDriver_FluxSchema::getTagKeys($this->client, $measurement, $bucket);
    }

    public function getVersion() {
        return CMetric_Driver_InfluxDBDriver_FluxSchema::getVersion($this->client);
    }

    public function query($query) {
        if ($query instanceof CMetric_QueryBuilder) {
            $query = $this->formatQuery($query);
        }

        return (new CMetric_Driver_InfluxDBDriver_FluxQueryResult($this->client->createQueryApi()->query($query)))->toQueryResult();
    }
}
