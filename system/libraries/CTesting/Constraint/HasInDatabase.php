<?php

use PHPUnit\Framework\Constraint\Constraint;

class CTesting_Constraint_HasInDatabase extends Constraint {
    /**
     * Number of records that will be shown in the console in case of failure.
     *
     * @var int
     */
    protected $show = 3;

    /**
     * The database connection.
     *
     * @var \CDatabase_Connection
     */
    protected $database;

    /**
     * The data that will be used to narrow the search in the database table.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new constraint instance.
     *
     * @param \CDatabase_Connection $database
     * @param array                 $data
     *
     * @return void
     */
    public function __construct($database, array $data) {
        $this->data = $data;
        $this->database = $database;
    }

    /**
     * Check if the data is found in the given table.
     *
     * @param string $table
     *
     * @return bool
     */
    public function matches($table): bool {
        return $this->database->table($table)->where($this->data)->count() > 0;
    }

    /**
     * Get the description of the failure.
     *
     * @param string $table
     *
     * @return string
     */
    public function failureDescription($table): string {
        return sprintf(
            "a row in the table [%s] matches the attributes %s.\n\n%s",
            $table,
            $this->toString(),
            $this->getAdditionalInfo($table)
        );
    }

    /**
     * Get additional info about the records found in the database table.
     *
     * @param string $table
     *
     * @return string
     */
    protected function getAdditionalInfo($table) {
        $query = $this->database->table($table);

        $results = $query->limit($this->show)->get();

        if ($results->count() === 0) {
            return 'The table is empty.';
        }

        $description = "Found: {$results->count()} rows shown (of the first {$this->show}):\n\n";

        foreach ($results as $result) {
            $description .= json_encode($result) . "\n";
        }

        return $description;
    }

    /**
     * Get a string representation of the object.
     *
     * @return string
     */
    public function toString(): string {
        return json_encode($this->data);
    }
}
