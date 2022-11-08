<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 1:19:25 AM
 */
trait CTrait_Element_DataProvider {
    /**
     * @var CManager_DataProviderAbstract
     */
    protected $dataProvider;

    /**
     * @param Closure    $closure
     * @param null|mixed $requires
     *
     * @return $this
     */
    public function setDataFromClosure($closure, $requires = null) {
        $this->dataProvider = CManager::createClosureDataProvider($closure, carr::wrap($requires));

        return $this;
    }

    /**
     * @param CModel     $model
     * @param null|mixed $queryCallback
     *
     * @return $this
     */
    public function setDataFromModel($model, $queryCallback = null) {
        if (is_string($model)) {
            $this->dataProvider = CManager::createModelDataProvider($model, $queryCallback);

            return $this;
        }
        $modelQuery = $model;
        if ($modelQuery instanceof CModel_Collection) {
            throw new Exception('error when calling setDataFromModel, please use CModel/CModel_Query instance (CModel_Collection passed)');
        }

        $sql = $this->db()->compileBinds($modelQuery->toSql(), $modelQuery->getBindings());

        return $this->setDataFromQuery($sql);
    }

    /**
     * @param string $q
     *
     * @return $this
     */
    public function setDataFromQuery($q) {
        $this->dataProvider = CManager::createSqlDataProvider($q);

        $dbResolver = $this->dbResolver;
        $dbName = $this->dbName;
        $dbConfig = $this->dbConfig;

        $this->dataProvider->setConnection(function () use ($dbResolver, $dbName, $dbConfig) {
            if ($dbResolver != null) {
                return $dbResolver->connection($dbName);
            }

            if (strlen($dbName) > 0) {
                return CDatabase::instance($dbName);
            }

            return CDatabase::instance($dbName, $dbConfig);
        });

        return $this;
    }
}
