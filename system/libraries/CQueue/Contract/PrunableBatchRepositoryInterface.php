<?php

interface CQueue_Contract_PrunableBatchRepositoryInterface extends CQueue_Contract_BatchRepositoryInterface {
    /**
     * Prune all of the entries older than the given date.
     *
     * @param \DateTimeInterface $before
     *
     * @return int
     */
    public function prune(DateTimeInterface $before);
}
