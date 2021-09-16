<?php

defined('SYSPATH') or die('No direct access allowed.');

 /**
  * @author Hery Kurniawan
  * @license Ittron Global Teknologi <ittron.co.id>
  *
  * @since Aug 18, 2018, 10:43:47 AM
  */

 use \Doctrine\Common\Cache\Cache;

/**
 * Configuration container for the CDatabase.
 */
class CDatabase_Configuration {
    /**
     * The attributes that are contained in the configuration.
     * Values are default values.
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Sets the SQL logger to use. Defaults to NULL which means SQL logging is disabled.
     *
     * @param CDatabase_Logger|null $logger
     *
     * @return void
     */
    public function setSQLLogger(SQLLogger $logger = null) {
        $this->_attributes['sqlLogger'] = $logger;
    }

    /**
     * Gets the SQL logger that is used.
     *
     * @return \Doctrine\DBAL\Logging\SQLLogger|null
     */
    public function getSQLLogger() {
        return isset($this->_attributes['sqlLogger']) ? $this->_attributes['sqlLogger'] : null;
    }

    /**
     * Gets the cache driver implementation that is used for query result caching.
     *
     * @return \Doctrine\Common\Cache\Cache|null
     */
    public function getResultCacheImpl() {
        return isset($this->_attributes['resultCacheImpl']) ? $this->_attributes['resultCacheImpl'] : null;
    }

    /**
     * Sets the cache driver implementation that is used for query result caching.
     *
     * @param \Doctrine\Common\Cache\Cache $cacheImpl
     *
     * @return void
     */
    public function setResultCacheImpl(Cache $cacheImpl) {
        $this->_attributes['resultCacheImpl'] = $cacheImpl;
    }

    /**
     * Sets the filter schema assets expression.
     *
     * Only include tables/sequences matching the filter expression regexp in
     * schema instances generated for the active connection when calling
     * {AbstractSchemaManager#createSchema()}.
     *
     * @param string $filterExpression
     *
     * @return void
     */
    public function setFilterSchemaAssetsExpression($filterExpression) {
        $this->_attributes['filterSchemaAssetsExpression'] = $filterExpression;
    }

    /**
     * Returns filter schema assets expression.
     *
     * @return string|null
     */
    public function getFilterSchemaAssetsExpression() {
        return isset($this->_attributes['filterSchemaAssetsExpression']) ? $this->_attributes['filterSchemaAssetsExpression'] : null;
    }

    /**
     * Sets the default auto-commit mode for connections.
     *
     * If a connection is in auto-commit mode, then all its SQL statements will be executed and committed as individual
     * transactions. Otherwise, its SQL statements are grouped into transactions that are terminated by a call to either
     * the method commit or the method rollback. By default, new connections are in auto-commit mode.
     *
     * @param bool $autoCommit true to enable auto-commit mode; false to disable it
     *
     * @see   getAutoCommit
     */
    public function setAutoCommit($autoCommit) {
        $this->_attributes['autoCommit'] = (boolean) $autoCommit;
    }

    /**
     * Returns the default auto-commit mode for connections.
     *
     * @return bool true if auto-commit mode is enabled by default for connections, false otherwise
     *
     * @see    setAutoCommit
     */
    public function getAutoCommit() {
        return isset($this->_attributes['autoCommit']) ? $this->_attributes['autoCommit'] : true;
    }
}
