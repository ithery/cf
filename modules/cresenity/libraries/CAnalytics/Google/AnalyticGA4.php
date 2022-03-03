<?php

use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

class CAnalytics_Google_AnalyticGA4 {
    use CTrait_Macroable;

    /**
     * @var \CAnalytics_Google_ClientGA4
     */
    protected $client;

    /**
     * @var string
     */
    protected $propertyId;

    private $dateRanges = [];

    private $metrics = [];

    private $dimensions = [];

    private $report;

    /**
     * @param \CAnalytics_Google_ClientGA4 $client
     * @param string                       $propertyId
     */
    public function __construct(CAnalytics_Google_ClientGA4 $client, $propertyId) {
        $this->client = $client;

        $this->propertyId = $propertyId;
    }

    /**
     * @param string $propertyId
     *
     * @return $this
     */
    public function setPropertyId($propertyId) {
        $this->propertyId = $propertyId;

        return $this;
    }

    public function getPropertyId() {
        return $this->propertyId;
    }

    public function getMetadata() {
        $googleMetadata = $this->client->getAnalyticsService()->getMetadata(
            'properties/' . $this->propertyId . '/metadata',
        );
        $result = [];
        $result['dimensions'] = c::collect($googleMetadata->getDimensions())->mapWithKeys(function ($dimension) {
            /** @var \Google\Analytics\Data\V1beta\DimensionMetadata $dimension */
            return [$dimension->getApiName() => [
                'apiName' => $dimension->getApiName(),
                'uiName' => $dimension->getUiName(),
                'category' => $dimension->getCategory(),
                'description' => $dimension->getDescription(),
                'isCustomDefinition' => $dimension->getCustomDefinition(),
            ]];
        })->toArray();
        $result['metrics'] = c::collect($googleMetadata->getMetrics())->mapWithKeys(function ($metric) {
            /** @var \Google\Analytics\Data\V1beta\MetricMetadata $metric */
            $type = $metric->getType();
            $typeName = \Google\Analytics\Data\V1beta\MetricType::name($type);

            return [$metric->getApiName() => [
                'apiName' => $metric->getApiName(),
                'uiName' => $metric->getUiName(),
                'category' => $metric->getCategory(),
                'description' => $metric->getDescription(),
                'isCustomDefinition' => $metric->getCustomDefinition(),
                'type' => $type,
                'typeName' => $typeName,
            ]];
        })->toArray();

        return $result;
    }

    /**
     * Undocumented function.
     *
     * @return BetaAnalyticsDataClient
     */
    public function getAnalyticsService() {
        return $this->client->getAnalyticsService();
    }

    /**
     * @return CAnalytics_Google_AnalyticGA4_ReportRunner
     */
    public function createReport() {
        return new CAnalytics_Google_AnalyticGA4_ReportRunner($this->client, $this->propertyId);
    }

    /**
     * @return CAnalytics_Google_MetaData
     */
    public static function metadata() {
        return new CBase_ForwarderStaticClass(CAnalytics_Google_MetaData::class);
    }

    /**
     * @return CAnalytics_Google_AnalyticGA4_PredefinedReport
     */
    public function predefinedReport() {
        return new CAnalytics_Google_AnalyticGA4_PredefinedReport($this);
    }
}
