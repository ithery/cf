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
     * InfluxDB constructor.
     *
     * @param array $options
     */
    public function __construct(array $options) {
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
}
