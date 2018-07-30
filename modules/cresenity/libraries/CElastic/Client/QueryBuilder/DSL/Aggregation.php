<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:46:01 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * elasticsearch aggregation DSL.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations.html
 */
class CElastic_Client_QueryBuilder_DSL_Aggregation implements CElastic_Client_QueryBuilder_DSL {

    /**
     * must return type for QueryBuilder usage.
     *
     * @return string
     */
    public function getType() {
        return self::TYPE_AGGREGATION;
    }

    /**
     * min aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-min-aggregation.html
     *
     * @param string $name
     *
     * @return Min
     */
    public function min($name) {
        return new CElastic_Client_Aggregation_Min($name);
    }

    /**
     * max aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-max-aggregation.html
     *
     * @param string $name
     *
     * @return Max
     */
    public function max($name) {
        return new CElastic_Client_Aggregation_Max($name);
    }

    /**
     * sum aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-sum-aggregation.html
     *
     * @param string $name
     *
     * @return Sum
     */
    public function sum($name) {
        return new CElastic_Client_Aggregation_Sum($name);
    }

    /**
     * sum bucket aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-sum-bucket-aggregation.html
     *
     * @param string      $name
     * @param string|null $bucketsPath
     *
     * @return SumBucket
     */
    public function sum_bucket($name, $bucketsPath = null) {
        return new CElastic_Client_Aggregation_SumBucket($name, $bucketsPath);
    }

    /**
     * avg aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-avg-aggregation.html
     *
     * @param string $name
     *
     * @return Avg
     */
    public function avg($name) {
        return new CElastic_Client_Aggregation_Avg($name);
    }

    /**
     * avg bucket aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-avg-bucket-aggregation.html
     *
     * @param string      $name
     * @param string|null $bucketsPath
     *
     * @return AvgBucket
     */
    public function avg_bucket($name, $bucketsPath = null) {
        return new CElastic_Client_Aggregation_AvgBucket($name, $bucketsPath);
    }

    /**
     * stats aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-stats-aggregation.html
     *
     * @param string $name
     *
     * @return Stats
     */
    public function stats($name) {
        return new CElastic_Client_Aggregation_Stats($name);
    }

    /**
     * extended stats aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-extendedstats-aggregation.html
     *
     * @param string $name
     *
     * @return ExtendedStats
     */
    public function extended_stats($name) {
        return new CElastic_Client_Aggregation_ExtendedStats($name);
    }

    /**
     * value count aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-valuecount-aggregation.html
     *
     * @param string $name
     * @param string $field
     *
     * @return ValueCount
     */
    public function value_count($name, $field) {
        return new CElastic_Client_Aggregation_ValueCount($name, $field);
    }

    /**
     * percentiles aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-percentile-aggregation.html
     *
     * @param string $name  the name of this aggregation
     * @param string $field the field on which to perform this aggregation
     *
     * @return Percentiles
     */
    public function percentiles($name, $field = null) {
        return new CElastic_Client_Aggregation_Percentiles($name, $field);
    }

    /**
     * percentile ranks aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-percentile-rank-aggregation.html
     *
     * @param string $name
     */
    public function percentile_ranks($name) {
        throw new NotImplementedException();
    }

    /**
     * cardinality aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-cardinality-aggregation.html
     *
     * @param string $name
     *
     * @return Cardinality
     */
    public function cardinality($name) {
        return new CElastic_Client_Aggregation_Cardinality($name);
    }

    /**
     * geo bounds aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-geobounds-aggregation.html
     *
     * @param string $name
     */
    public function geo_bounds($name) {
        throw new NotImplementedException();
    }

    /**
     * top hits aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-top-hits-aggregation.html
     *
     * @param string $name
     *
     * @return TopHits
     */
    public function top_hits($name) {
        return new CElastic_Client_Aggregation_TopHits($name);
    }

    /**
     * scripted metric aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-scripted-metric-aggregation.html
     *
     * @param string      $name
     * @param string|null $initScript
     * @param string|null $mapScript
     * @param string|null $combineScript
     * @param string|null $reduceScript
     *
     * @return ScriptedMetric
     */
    public function scripted_metric($name, $initScript = null, $mapScript = null, $combineScript = null, $reduceScript = null) {
        return new CElastic_Client_Aggregation_ScriptedMetric($name, $initScript, $mapScript, $combineScript, $reduceScript);
    }

    /**
     * global aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-global-aggregation.html
     *
     * @param string $name
     *
     * @return GlobalAggregation
     */
    public function global_agg($name) {
        return new CElastic_Client_Aggregation_GlobalAggregation($name);
    }

    /**
     * filter aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html
     *
     * @param string        $name
     * @param AbstractQuery $filter
     *
     * @return FilterAggregation
     */
    public function filter($name, AbstractQuery $filter = null) {
        return new CElastic_Client_Aggregation_FilterAggregation($name, $filter);
    }

    /**
     * filters aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filters-aggregation.html
     *
     * @param string $name
     *
     * @return Filters
     */
    public function filters($name) {
        return new CElastic_Client_Aggregation_Filters($name);
    }

    /**
     * missing aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-missing-aggregation.html
     *
     * @param string $name
     * @param string $field
     *
     * @return Missing
     */
    public function missing($name, $field) {
        return new CElastic_Client_Aggregation_Missing($name, $field);
    }

    /**
     * nested aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-nested-aggregation.html
     *
     * @param string $name
     * @param string $path the nested path for this aggregation
     *
     * @return Nested
     */
    public function nested($name, $path) {
        return new CElastic_Client_Aggregation_Nested($name, $path);
    }

    /**
     * reverse nested aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-reverse-nested-aggregation.html
     *
     * @param string $name The name of this aggregation
     * @param string $path Optional path to the nested object for this aggregation. Defaults to the root of the main document.
     *
     * @return ReverseNested
     */
    public function reverse_nested($name, $path = null) {
        return new CElastic_Client_Aggregation_ReverseNested($name);
    }

    /**
     * children aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-children-aggregation.html
     *
     * @param string $name
     */
    public function children($name) {
        throw new NotImplementedException();
    }

    /**
     * terms aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html
     *
     * @param string $name
     *
     * @return Terms
     */
    public function terms($name) {
        return new CElastic_Client_Aggregation_Terms($name);
    }

    /**
     * significant terms aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-significantterms-aggregation.html
     *
     * @param string $name
     *
     * @return SignificantTerms
     */
    public function significant_terms($name) {
        return new CElastic_Client_Aggregation_SignificantTerms($name);
    }

    /**
     * range aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-range-aggregation.html
     *
     * @param string $name
     *
     * @return Range
     */
    public function range($name) {
        return new CElastic_Client_Aggregation_Range($name);
    }

    /**
     * date range aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-daterange-aggregation.html
     *
     * @param string $name
     *
     * @return DateRange
     */
    public function date_range($name) {
        return new CElastic_Client_Aggregation_DateRange($name);
    }

    /**
     * ipv4 range aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-iprange-aggregation.html
     *
     * @param string $name
     * @param string $field
     *
     * @return IpRange
     */
    public function ipv4_range($name, $field) {
        return new CElastic_Client_Aggregation_IpRange($name, $field);
    }

    /**
     * histogram aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-histogram-aggregation.html
     *
     * @param string $name     the name of this aggregation
     * @param string $field    the name of the field on which to perform the aggregation
     * @param int    $interval the interval by which documents will be bucketed
     *
     * @return Histogram
     */
    public function histogram($name, $field, $interval) {
        return new CElastic_Client_Aggregation_Histogram($name, $field, $interval);
    }

    /**
     * date histogram aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-datehistogram-aggregation.html
     *
     * @param string $name     the name of this aggregation
     * @param string $field    the name of the field on which to perform the aggregation
     * @param int    $interval the interval by which documents will be bucketed
     *
     * @return DateHistogram
     */
    public function date_histogram($name, $field, $interval) {
        return new CElastic_Client_Aggregation_DateHistogram($name, $field, $interval);
    }

    /**
     * geo distance aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-geodistance-aggregation.html
     *
     * @param string       $name   the name if this aggregation
     * @param string       $field  the field on which to perform this aggregation
     * @param string|array $origin the point from which distances will be calculated
     *
     * @return GeoDistance
     */
    public function geo_distance($name, $field, $origin) {
        return new CElastic_Client_Aggregation_GeoDistance($name, $field, $origin);
    }

    /**
     * geohash grid aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-geohashgrid-aggregation.html
     *
     * @param string $name  the name of this aggregation
     * @param string $field the field on which to perform this aggregation
     *
     * @return GeohashGrid
     */
    public function geohash_grid($name, $field) {
        return new CElastic_Client_Aggregation_GeohashGrid($name, $field);
    }

    /**
     * bucket script aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-bucket-script-aggregation.html
     *
     * @param string      $name
     * @param array|null  $bucketsPath
     * @param string|null $script
     *
     * @return BucketScript
     */
    public function bucket_script($name, $bucketsPath = null, $script = null) {
        return new CElastic_Client_Aggregation_BucketScript($name, $bucketsPath, $script);
    }

    /**
     * serial diff aggregation.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-serialdiff-aggregation.html
     *
     * @param string      $name
     * @param string|null $bucketsPath
     *
     * @return SerialDiff
     */
    public function serial_diff($name, $bucketsPath = null) {
        return new CElastic_Client_Aggregation_SerialDiff($name, $bucketsPath);
    }

}
