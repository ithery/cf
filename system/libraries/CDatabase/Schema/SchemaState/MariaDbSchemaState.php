<?php

class CDatabase_Schema_SchemaState_MariaDbSchemaState extends CDatabase_Schema_SchemaState_MySqlSchemaState {
    /**
     * Get the base dump command arguments for MariaDB as a string.
     *
     * @return string
     */
    protected function baseDumpCommand() {
        $command = 'mysqldump ' . $this->connectionString() . ' --no-tablespaces --skip-add-locks --skip-comments --skip-set-charset --tz-utc --column-statistics=0';

        return $command . ' "${:CF_LOAD_DATABASE}"';
    }
}
