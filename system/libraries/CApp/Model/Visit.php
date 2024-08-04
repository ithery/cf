<?php

class CApp_Model_Visit extends CModel {
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'method', 'request', 'url', 'referer',
        'languages', 'useragent', 'headers',
        'device', 'platform', 'browser', 'ip',
        'visitor_id', 'visitor_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'request' => 'array',
        'languages' => 'array',
        'headers' => 'array',
    ];

    public function __construct(array $attributes = []) {
        if (!isset($this->table)) {
            $this->setTable(CF::config('app.visitor.table_name'));
        }
        parent::__construct($attributes);
    }

    /**
     * Get the owning visitable model.
     *
     * @return \CDatabase_Relation_MorphTo
     */
    public function visitable() {
        return $this->morphTo('visitable');
    }

    /**
     * Get the owning user model.
     *
     * @return \CDatabase_Relation_MorphTo
     */
    public function visitor() {
        return $this->morphTo('visitor');
    }
}
