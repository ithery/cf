<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:37:37 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Schema extends CDatabase_AbstractAsset {

    /**
     * The namespaces in this schema.
     *
     * @var array
     */
    private $namespaces = [];

    /**
     * @var \Doctrine\DBAL\Schema\Table[]
     */
    protected $_tables = [];

    /**
     * @var \Doctrine\DBAL\Schema\Sequence[]
     */
    protected $_sequences = [];

    /**
     * @var SchemaConfig
     */
    protected $_schemaConfig = false;

    /**
     * @param \Doctrine\DBAL\Schema\Table[]      $tables
     * @param \Doctrine\DBAL\Schema\Sequence[]   $sequences
     * @param \Doctrine\DBAL\Schema\SchemaConfig $schemaConfig
     * @param array                              $namespaces
     */
    public function __construct(array $tables = [], array $sequences = [], CDatabase_Schema_Config $schemaConfig = null, array $namespaces = []) {
        if ($schemaConfig == null) {
            $schemaConfig = new SchemaConfig();
        }
        $this->_schemaConfig = $schemaConfig;
        $this->_setName($schemaConfig->getName() ?: 'public');

        foreach ($namespaces as $namespace) {
            $this->createNamespace($namespace);
        }

        foreach ($tables as $table) {
            $this->_addTable($table);
        }

        foreach ($sequences as $sequence) {
            $this->_addSequence($sequence);
        }
    }

    /**
     * @return bool
     */
    public function hasExplicitForeignKeyIndexes() {
        return $this->_schemaConfig->hasExplicitForeignKeyIndexes();
    }

    /**
     * @param CDatabase_Schema_Table $table
     *
     * @return void
     *
     * @throws CDatabase_Schema_Exception
     */
    protected function _addTable(CDatabase_Schema_Table $table) {
        $namespaceName = $table->getNamespaceName();
        $tableName = $table->getFullQualifiedName($this->getName());

        if (isset($this->_tables[$tableName])) {
            throw SchemaException::tableAlreadyExists($tableName);
        }

        if (!$table->isInDefaultNamespace($this->getName()) && !$this->hasNamespace($namespaceName)) {
            $this->createNamespace($namespaceName);
        }

        $this->_tables[$tableName] = $table;
        $table->setSchemaConfig($this->_schemaConfig);
    }

    /**
     * @param CDatabase_Schema_Sequence $sequence
     *
     * @return void
     *
     * @throws CDatabase_Schema_Exception
     */
    protected function _addSequence(CDatabase_Schema_Sequence $sequence) {
        $namespaceName = $sequence->getNamespaceName();
        $seqName = $sequence->getFullQualifiedName($this->getName());

        if (isset($this->_sequences[$seqName])) {
            throw CDatabase_Schema_Exception::sequenceAlreadyExists($seqName);
        }

        if (!$sequence->isInDefaultNamespace($this->getName()) && !$this->hasNamespace($namespaceName)) {
            $this->createNamespace($namespaceName);
        }

        $this->_sequences[$seqName] = $sequence;
    }

    /**
     * Returns the namespaces of this schema.
     *
     * @return array A list of namespace names.
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     * Gets all tables of this schema.
     *
     * @return \Doctrine\DBAL\Schema\Table[]
     */
    public function getTables() {
        return $this->_tables;
    }

    /**
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\Table
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function getTable($tableName) {
        $tableName = $this->getFullQualifiedAssetName($tableName);
        if (!isset($this->_tables[$tableName])) {
            throw SchemaException::tableDoesNotExist($tableName);
        }

        return $this->_tables[$tableName];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getFullQualifiedAssetName($name) {
        $name = $this->getUnquotedAssetName($name);

        if (strpos($name, ".") === false) {
            $name = $this->getName() . "." . $name;
        }

        return strtolower($name);
    }

    /**
     * Returns the unquoted representation of a given asset name.
     *
     * @param string $assetName Quoted or unquoted representation of an asset name.
     *
     * @return string
     */
    private function getUnquotedAssetName($assetName) {
        if ($this->isIdentifierQuoted($assetName)) {
            return $this->trimQuotes($assetName);
        }

        return $assetName;
    }

    /**
     * Does this schema have a namespace with the given name?
     *
     * @param string $namespaceName
     *
     * @return bool
     */
    public function hasNamespace($namespaceName) {
        $namespaceName = strtolower($this->getUnquotedAssetName($namespaceName));

        return isset($this->namespaces[$namespaceName]);
    }

    /**
     * Does this schema have a table with the given name?
     *
     * @param string $tableName
     *
     * @return bool
     */
    public function hasTable($tableName) {
        $tableName = $this->getFullQualifiedAssetName($tableName);

        return isset($this->_tables[$tableName]);
    }

    /**
     * Gets all table names, prefixed with a schema name, even the default one if present.
     *
     * @return array
     */
    public function getTableNames() {
        return array_keys($this->_tables);
    }

    /**
     * @param string $sequenceName
     *
     * @return bool
     */
    public function hasSequence($sequenceName) {
        $sequenceName = $this->getFullQualifiedAssetName($sequenceName);

        return isset($this->_sequences[$sequenceName]);
    }

    /**
     * @param string $sequenceName
     *
     * @return \Doctrine\DBAL\Schema\Sequence
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function getSequence($sequenceName) {
        $sequenceName = $this->getFullQualifiedAssetName($sequenceName);
        if (!$this->hasSequence($sequenceName)) {
            throw SchemaException::sequenceDoesNotExist($sequenceName);
        }

        return $this->_sequences[$sequenceName];
    }

    /**
     * @return \Doctrine\DBAL\Schema\Sequence[]
     */
    public function getSequences() {
        return $this->_sequences;
    }

    /**
     * Creates a new namespace.
     *
     * @param string $namespaceName The name of the namespace to create.
     *
     * @return \Doctrine\DBAL\Schema\Schema This schema instance.
     *
     * @throws SchemaException
     */
    public function createNamespace($namespaceName) {
        $unquotedNamespaceName = strtolower($this->getUnquotedAssetName($namespaceName));

        if (isset($this->namespaces[$unquotedNamespaceName])) {
            throw SchemaException::namespaceAlreadyExists($unquotedNamespaceName);
        }

        $this->namespaces[$unquotedNamespaceName] = $namespaceName;

        return $this;
    }

    /**
     * Creates a new table.
     *
     * @param string $tableName
     *
     * @return CDatabase_Schema_Table
     */
    public function createTable($tableName) {
        $table = new CDatabase_Schema_Table($tableName);
        $this->_addTable($table);

        foreach ($this->_schemaConfig->getDefaultTableOptions() as $name => $value) {
            $table->addOption($name, $value);
        }

        return $table;
    }

    /**
     * Renames a table.
     *
     * @param string $oldTableName
     * @param string $newTableName
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function renameTable($oldTableName, $newTableName) {
        $table = $this->getTable($oldTableName);
        $table->_setName($newTableName);

        $this->dropTable($oldTableName);
        $this->_addTable($table);

        return $this;
    }

    /**
     * Drops a table from the schema.
     *
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function dropTable($tableName) {
        $tableName = $this->getFullQualifiedAssetName($tableName);
        $this->getTable($tableName);
        unset($this->_tables[$tableName]);

        return $this;
    }

    /**
     * Creates a new sequence.
     *
     * @param string $sequenceName
     * @param int    $allocationSize
     * @param int    $initialValue
     *
     * @return CDatabase_Schema_Sequence
     */
    public function createSequence($sequenceName, $allocationSize = 1, $initialValue = 1) {
        $seq = new CDatabase_Schema_Sequence($sequenceName, $allocationSize, $initialValue);
        $this->_addSequence($seq);

        return $seq;
    }

    /**
     * @param string $sequenceName
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function dropSequence($sequenceName) {
        $sequenceName = $this->getFullQualifiedAssetName($sequenceName);
        unset($this->_sequences[$sequenceName]);

        return $this;
    }

    /**
     * Returns an array of necessary SQL queries to create the schema on the given platform.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function toSql(AbstractPlatform $platform) {
        $sqlCollector = new CreateSchemaSqlCollector($platform);
        $this->visit($sqlCollector);

        return $sqlCollector->getQueries();
    }

    /**
     * Return an array of necessary SQL queries to drop the schema on the given platform.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function toDropSql(AbstractPlatform $platform) {
        $dropSqlCollector = new DropSchemaSqlCollector($platform);
        $this->visit($dropSqlCollector);

        return $dropSqlCollector->getQueries();
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema              $toSchema
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function getMigrateToSql(CDatabase_Schema $toSchema, CDatabase_Platform $platform) {
        $comparator = new CDatabase_Schema_Comparator();
        $schemaDiff = $comparator->compare($this, $toSchema);

        return $schemaDiff->toSql($platform);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema              $fromSchema
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function getMigrateFromSql(Schema $fromSchema, AbstractPlatform $platform) {
        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $this);

        return $schemaDiff->toSql($platform);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Visitor\Visitor $visitor
     *
     * @return void
     */
    public function visit(Visitor $visitor) {
        $visitor->acceptSchema($this);

        if ($visitor instanceof NamespaceVisitor) {
            foreach ($this->namespaces as $namespace) {
                $visitor->acceptNamespace($namespace);
            }
        }

        foreach ($this->_tables as $table) {
            $table->visit($visitor);
        }

        foreach ($this->_sequences as $sequence) {
            $sequence->visit($visitor);
        }
    }

    /**
     * Cloning a Schema triggers a deep clone of all related assets.
     *
     * @return void
     */
    public function __clone() {
        foreach ($this->_tables as $k => $table) {
            $this->_tables[$k] = clone $table;
        }
        foreach ($this->_sequences as $k => $sequence) {
            $this->_sequences[$k] = clone $sequence;
        }
    }

}
