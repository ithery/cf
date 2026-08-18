<?php

/**
 * Named dataset registry for CReport.
 *
 * Applications register datasets by name so a report layout (jrxml) only needs
 * to reference the dataset name, while the actual data binding stays in code.
 *
 * <code>
 * CReport_DatasetRegistry::register('country', Cresenity\Demo\Model\Country::class);
 * CReport_DatasetRegistry::register('sales', function () {
 *     return c::collect([...]);
 * });
 * CReport::builder()->fromXml($xml)->setDataFromDataset('country');
 * </code>
 */
class CReport_DatasetRegistry {
    /**
     * Registered dataset resolvers, keyed by name.
     *
     * @var array
     */
    protected static $datasets = [];

    /**
     * Register a dataset.
     *
     * @param string $name
     * @param mixed  $resolver Closure returning the data, or the data itself:
     *                         CManager_Contract_DataProviderInterface, CCollection,
     *                         array of rows, CModel/CModel_Query instance, or CModel class name
     *
     * @return void
     */
    public static function register($name, $resolver) {
        static::$datasets[$name] = $resolver;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function has($name) {
        return array_key_exists($name, static::$datasets);
    }

    /**
     * @return string[]
     */
    public static function names() {
        return array_keys(static::$datasets);
    }

    /**
     * Resolve a dataset into a data provider.
     *
     * @param string $name
     *
     * @throws Exception when the dataset is not registered
     *
     * @return CManager_Contract_DataProviderInterface
     */
    public static function resolve($name) {
        if (!static::has($name)) {
            throw new Exception('Dataset [' . $name . '] is not registered in CReport_DatasetRegistry');
        }
        $resolved = static::$datasets[$name];
        if ($resolved instanceof Closure) {
            $resolved = $resolved();
        }
        if ($resolved instanceof CManager_Contract_DataProviderInterface) {
            return $resolved;
        }
        if ($resolved instanceof CCollection) {
            return CManager::createCollectionDataProvider($resolved);
        }
        if (is_array($resolved)) {
            return CManager::createCollectionDataProvider(c::collect($resolved));
        }

        //CModel instance, CModel_Query, or model class name
        return CManager::createModelDataProvider($resolved);
    }

    /**
     * Get the field names of a dataset, taken from its first row.
     *
     * @param string $name
     *
     * @return string[]
     */
    public static function fields($name) {
        $provider = static::resolve($name);
        $rows = $provider->toEnumerable();
        $first = null;
        foreach ($rows as $row) {
            $first = $row;

            break;
        }
        if ($first === null) {
            return [];
        }
        if ($first instanceof CModel) {
            return array_keys($first->getAttributes());
        }
        if ($first instanceof CCollection) {
            return array_keys($first->toArray());
        }
        if (is_array($first)) {
            return array_keys($first);
        }
        if (is_object($first)) {
            return array_keys(get_object_vars($first));
        }

        return [];
    }

    /**
     * Describe all registered datasets with their fields, for UI consumption.
     *
     * @return array e.g. [['name' => 'country', 'fields' => ['code', 'name']], ...]
     */
    public static function describe() {
        $result = [];
        foreach (static::names() as $name) {
            $result[] = ['name' => $name, 'fields' => static::fields($name)];
        }

        return $result;
    }
}
