<?php

/**
 * Database-agnostic date expression generator for Chartable queries.
 *
 * Supports: mysql, mariadb, pgsql, sqlite, sqlsrv
 */
class CModel_Chartable_DateExpression {
    /**
     * @var string
     */
    protected $driver;

    /**
     * @param string $driver
     */
    public function __construct($driver) {
        $this->driver = $driver;
    }

    /**
     * @param string $dateColumn
     *
     * @return string
     */
    public function year($dateColumn) {
        switch ($this->driver) {
            case 'pgsql':
                return "TO_CHAR({$dateColumn}, 'YYYY')";
            case 'sqlite':
                return "strftime('%Y', {$dateColumn})";
            case 'sqlsrv':
                return "FORMAT({$dateColumn}, 'yyyy')";
            default:
                return "YEAR({$dateColumn})";
        }
    }

    /**
     * @param string $dateColumn
     *
     * @return string
     */
    public function yearMonth($dateColumn) {
        switch ($this->driver) {
            case 'pgsql':
                return "TO_CHAR({$dateColumn}, 'YYYYMM')";
            case 'sqlite':
                return "strftime('%Y%m', {$dateColumn})";
            case 'sqlsrv':
                return "FORMAT({$dateColumn}, 'yyyyMM')";
            default:
                return "CONCAT(YEAR({$dateColumn}),LPAD(MONTH({$dateColumn}),2,'0'))";
        }
    }

    /**
     * @param string $dateColumn
     *
     * @return string
     */
    public function yearMonthWeek($dateColumn) {
        switch ($this->driver) {
            case 'pgsql':
                return "TO_CHAR({$dateColumn}, 'YYYYMM') || '-W' || FLOOR((EXTRACT(DAY FROM {$dateColumn}) - 1) / 7) + 1";
            case 'sqlite':
                return "strftime('%Y%m', {$dateColumn}) || '-W' || ((CAST(strftime('%d', {$dateColumn}) AS INTEGER) - 1) / 7 + 1)";
            case 'sqlsrv':
                return "FORMAT({$dateColumn}, 'yyyyMM') + '-W' + CAST(((DAY({$dateColumn}) - 1) / 7 + 1) AS VARCHAR)";
            default:
                return "CONCAT(YEAR({$dateColumn}),LPAD(MONTH({$dateColumn}),2,'0'),'-W',FLOOR((DAY({$dateColumn}) - 1) / 7) + 1)";
        }
    }

    /**
     * @param string $dateColumn
     *
     * @return string
     */
    public function date($dateColumn) {
        switch ($this->driver) {
            case 'pgsql':
                return "TO_CHAR({$dateColumn}, 'YYYY-MM-DD')";
            case 'sqlite':
                return "strftime('%Y-%m-%d', {$dateColumn})";
            case 'sqlsrv':
                return "FORMAT({$dateColumn}, 'yyyy-MM-dd')";
            default:
                return "DATE_FORMAT({$dateColumn}, '%Y-%m-%d')";
        }
    }

    /**
     * @param string $dateColumn
     *
     * @return string
     */
    public function dateHour($dateColumn) {
        switch ($this->driver) {
            case 'pgsql':
                return "TO_CHAR({$dateColumn}, 'YYYY-MM-DD HH24')";
            case 'sqlite':
                return "strftime('%Y-%m-%d %H', {$dateColumn})";
            case 'sqlsrv':
                return "FORMAT({$dateColumn}, 'yyyy-MM-dd HH')";
            default:
                return "DATE_FORMAT({$dateColumn}, '%Y-%m-%d %H')";
        }
    }
}
