<?php

/**
 * @method CDatabase_Schema_ForeignKeyDefinition deferrable(bool $value = true) Set the foreign key as deferrable (PostgreSQL)
 * @method CDatabase_Schema_ForeignKeyDefinition initiallyImmediate(bool $value = true) Set the default time to check the constraint (PostgreSQL)
 * @method CDatabase_Schema_ForeignKeyDefinition on(string $table)                                                                                Specify the referenced table
 * @method CDatabase_Schema_ForeignKeyDefinition onDelete(string $action)                                                                         Add an ON DELETE action
 * @method CDatabase_Schema_ForeignKeyDefinition onUpdate(string $action)                                                                         Add an ON UPDATE action
 * @method CDatabase_Schema_ForeignKeyDefinition references(string|array $columns) Specify the referenced column(s)
 */
class CDatabase_Schema_ForeignKeyDefinition extends CBase_Fluent {
    /**
     * Indicate that updates should cascade.
     *
     * @return $this
     */
    public function cascadeOnUpdate() {
        return $this->onUpdate('cascade');
    }

    /**
     * Indicate that updates should be restricted.
     *
     * @return $this
     */
    public function restrictOnUpdate() {
        return $this->onUpdate('restrict');
    }

    /**
     * Indicate that deletes should cascade.
     *
     * @return $this
     */
    public function cascadeOnDelete() {
        return $this->onDelete('cascade');
    }

    /**
     * Indicate that deletes should be restricted.
     *
     * @return $this
     */
    public function restrictOnDelete() {
        return $this->onDelete('restrict');
    }

    /**
     * Indicate that deletes should set the foreign key value to null.
     *
     * @return $this
     */
    public function nullOnDelete() {
        return $this->onDelete('set null');
    }
}
