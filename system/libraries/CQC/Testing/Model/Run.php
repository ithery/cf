<?php
class CQC_Testing_Model_Run extends CQC_Testing_AbstractModel {
    protected $table = 'run';

    protected $dates = [
        'created',
        'updated',
        'started_at',
        'ended_at',
        'notified_at',
    ];

    protected $fillable = [
        'test_id',
        'was_ok',
        'log',
        'html',
        'screenshots',
        'started_at',
        'ended_at',
        'notified_at',
    ];
}
