<?php

class CModel_Scout {
    /**
     * The job class that should make models searchable.
     *
     * @var string
     */
    public static $makeSearchableJob = CModel_Scout_TaskQueue_MakeSearchable::class;

    /**
     * The job that should remove models from the search index.
     *
     * @var string
     */
    public static $removeFromSearchJob = CModel_Scout_TaskQueue_RemoveFromSearch::class;

    /**
     * Specify the job class that should make models searchable.
     *
     * @param string $class
     *
     * @return void
     */
    public static function makeSearchableUsing(string $class) {
        static::$makeSearchableJob = $class;
    }

    /**
     * Specify the job class that should remove models from the search index.
     *
     * @param string $class
     *
     * @return void
     */
    public static function removeFromSearchUsing(string $class) {
        static::$removeFromSearchJob = $class;
    }
}
