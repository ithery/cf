<?php
interface CQueue_Contract_PrunableFailedJobInterface {
    /**
     * Prune all of the entries older than the given date.
     *
     * @param \DateTimeInterface $before
     *
     * @return int
     */
    public function prune(DateTimeInterface $before);
}
