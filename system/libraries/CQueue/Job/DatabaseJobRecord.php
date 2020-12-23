<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 6:13:48 AM
 */
class CQueue_Job_DatabaseJobRecord {
    use CTrait_Helper_InteractsWithTime;

    /**
     * The underlying job record.
     *
     * @var \stdClass
     */
    protected $record;

    /**
     * Create a new job record instance.
     *
     * @param \stdClass $record
     *
     * @return void
     */
    public function __construct($record) {
        $this->record = $record;
    }

    /**
     * Increment the number of times the job has been attempted.
     *
     * @return int
     */
    public function increment() {
        $this->record->attempts++;
        return $this->record->attempts;
    }

    /**
     * Update the "reserved at" timestamp of the job.
     *
     * @return int
     */
    public function touch() {
        $this->record->reserved_at = $this->currentTime();
        return $this->record->reserved_at;
    }

    /**
     * Dynamically access the underlying job information.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key) {
        return $this->record->{$key};
    }
}
