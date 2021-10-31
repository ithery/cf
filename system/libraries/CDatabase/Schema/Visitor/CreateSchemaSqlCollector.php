<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 1:43:59 PM
 */
class CDatabase_Schema_Visitor_CreateSchemaSqlCollector extends CDatabase_Schema_Visitor {
    /**
     * @var array
     */
    private $createNamespaceQueries = [];

    /**
     * @var array
     */
    private $createTableQueries = [];

    /**
     * @var array
     */
    private $createSequenceQueries = [];

    /**
     * @var array
     */
    private $createFkConstraintQueries = [];

    /**
     * @var CDatabase_Platform
     */
    private $platform = null;

    /**
     * @param CDatabase_Platform $platform
     */
    public function __construct(CDatabase_Platform $platform) {
        $this->platform = $platform;
    }

    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName) {
        if ($this->platform->supportsSchemas()) {
            $this->createNamespaceQueries[] = $this->platform->getCreateSchemaSQL($namespaceName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTable(CDatabase_Schema_Table $table) {
        $this->createTableQueries = array_merge($this->createTableQueries, (array) $this->platform->getCreateTableSQL($table));
    }

    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(CDatabase_Schema_Table $localTable, CDatabase_Schema_ForeignKeyConstraint $fkConstraint) {
        if ($this->platform->supportsForeignKeyConstraints()) {
            $this->createFkConstraintQueries[] = $this->platform->getCreateForeignKeySQL($fkConstraint, $localTable);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSequence(CDatabase_Schema_Sequence $sequence) {
        $this->createSequenceQueries[] = $this->platform->getCreateSequenceSQL($sequence);
    }

    /**
     * @return void
     */
    public function resetQueries() {
        $this->createNamespaceQueries = [];
        $this->createTableQueries = [];
        $this->createSequenceQueries = [];
        $this->createFkConstraintQueries = [];
    }

    /**
     * Gets all queries collected so far.
     *
     * @return array
     */
    public function getQueries() {
        return array_merge(
            $this->createNamespaceQueries,
            $this->createTableQueries,
            $this->createSequenceQueries,
            $this->createFkConstraintQueries
        );
    }
}
