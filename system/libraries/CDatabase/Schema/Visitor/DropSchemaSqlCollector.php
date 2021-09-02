<?php

/**
 * Gathers SQL statements that allow to completely drop the current schema.
 */
class CDatabase_Schema_Visitor_DropSchemaSqlCollector extends CDatabase_Schema_Visitor {
    /**
     * @var SplObjectStorage
     */
    private $constraints;

    /**
     * @var SplObjectStorage
     */
    private $sequences;

    /**
     * @var SplObjectStorage
     */
    private $tables;

    /**
     * @var CDatabase_Platform
     */
    private $platform;

    public function __construct(CDatabase_Platform $platform) {
        $this->platform = $platform;
        $this->initializeQueries();
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTable(CDatabase_Schema_Table $table) {
        $this->tables->attach($table);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(CDatabase_Schema_Table $localTable, CDatabase_Schema_ForeignKeyConstraint $fkConstraint) {
        if (strlen($fkConstraint->getName()) === 0) {
            throw SchemaException::namedForeignKeyRequired($localTable, $fkConstraint);
        }

        $this->constraints->attach($fkConstraint, $localTable);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSequence(CDatabase_Schema_Sequence $sequence) {
        $this->sequences->attach($sequence);
    }

    /**
     * @return void
     */
    public function clearQueries() {
        $this->initializeQueries();
    }

    /**
     * @return string[]
     */
    public function getQueries() {
        $sql = [];

        foreach ($this->constraints as $fkConstraint) {
            assert($fkConstraint instanceof CDatabase_Schema_ForeignKeyConstraint);
            $localTable = $this->constraints[$fkConstraint];
            $sql[] = $this->platform->getDropForeignKeySQL($fkConstraint, $localTable);
        }

        foreach ($this->sequences as $sequence) {
            assert($sequence instanceof CDatabase_Schema_Sequence);
            $sql[] = $this->platform->getDropSequenceSQL($sequence);
        }

        foreach ($this->tables as $table) {
            assert($table instanceof CDatabase_Schema_Table);
            $sql[] = $this->platform->getDropTableSQL($table);
        }

        return $sql;
    }

    private function initializeQueries(): void {
        $this->constraints = new SplObjectStorage();
        $this->sequences = new SplObjectStorage();
        $this->tables = new SplObjectStorage();
    }
}
