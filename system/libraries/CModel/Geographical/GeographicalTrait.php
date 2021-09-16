<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 24, 2019, 11:31:50 AM
 */
trait CModel_Geographical_GeographicalTrait {
    /**
     * @param CModel_Query $query
     * @param float        $latitude  Latitude
     * @param float        $longitude Longitude
     *
     * @return CModel_Query
     */
    public function scopeDistance($query, $latitude, $longitude) {
        $latName = $this->getQualifiedLatitudeColumn();
        $lonName = $this->getQualifiedLongitudeColumn();

        $query->select($this->getTable() . '.*');
        $sql = '((ACOS(SIN(? * PI() / 180) * SIN(' . $latName . ' * PI() / 180) + COS(? * PI() / 180) * COS('
                . $latName . ' * PI() / 180) * COS((? - ' . $lonName . ') * PI() / 180)) * 180 / PI()) * 60 * ?) as distance';
        $kilometers = false;

        if (property_exists(static::class, 'kilometers')) {
            $kilometers = static::$kilometers;
        }
        if ($kilometers) {
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515 * 1.609344]);
        } else {
            // miles
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515]);
        }
        //echo $query->toSql();
        //var_export($query->getBindings());
        return $query;
    }

    public function scopeGeofence($query, $latitude, $longitude, $inner_radius, $outer_radius) {
        $query = $this->scopeDistance($query, $latitude, $longitude);
        return $query->havingRaw('distance BETWEEN ? AND ?', [$inner_radius, $outer_radius]);
    }

    public function scopeOrderByDistanceRuntime($query, $latitude, $longitude, $direction = 'asc') {
        $latName = $this->getQualifiedLatitudeColumn();
        $lonName = $this->getQualifiedLongitudeColumn();
        $sql = '((ACOS(SIN(? * PI() / 180) * SIN(' . $latName . ' * PI() / 180) + COS(? * PI() / 180) * COS('
                . $latName . ' * PI() / 180) * COS((? - ' . $lonName . ') * PI() / 180)) * 180 / PI()) * 60 * ?) ';
        $kilometers = false;
        if (property_exists(static::class, 'kilometers')) {
            $kilometers = static::$kilometers;
        }
        if ($kilometers) {
            $sql = $this->getConnection()->compileBinds($sql, [$latitude, $latitude, $longitude, 1.1515 * 1.609344]);
        } else {
            // miles
            $sql = $this->getConnection()->compileBinds($sql, [$latitude, $latitude, $longitude, 1.1515]);
        }
        return $query->orderByRaw($sql . ' ' . $direction);
    }

    public function scopeGeofenceRuntime($query, $latitude, $longitude, $inner_radius, $outer_radius) {
        $latName = $this->getQualifiedLatitudeColumn();
        $lonName = $this->getQualifiedLongitudeColumn();
        $sql = '((ACOS(SIN(? * PI() / 180) * SIN(' . $latName . ' * PI() / 180) + COS(? * PI() / 180) * COS('
                . $latName . ' * PI() / 180) * COS((? - ' . $lonName . ') * PI() / 180)) * 180 / PI()) * 60 * ?) ';
        $kilometers = false;
        if (property_exists(static::class, 'kilometers')) {
            $kilometers = static::$kilometers;
        }
        if ($kilometers) {
            $sql = $this->getConnection()->compileBinds($sql, [$latitude, $latitude, $longitude, 1.1515 * 1.609344]);
        } else {
            // miles
            $sql = $this->getConnection()->compileBinds($sql, [$latitude, $latitude, $longitude, 1.1515]);
        }
        return $query->whereRaw($sql . ' BETWEEN ? AND ?', [$inner_radius, $outer_radius]);
    }

    protected function getQualifiedLatitudeColumn() {
        return $this->getTable() . '.' . $this->getLatitudeColumn();
    }

    protected function getQualifiedLongitudeColumn() {
        return $this->getTable() . '.' . $this->getLongitudeColumn();
    }

    public function getLatitudeColumn() {
        return defined('static::LATITUDE') ? static::LATITUDE : 'latitude';
    }

    public function getLongitudeColumn() {
        return defined('static::LONGITUDE') ? static::LONGITUDE : 'longitude';
    }
}
