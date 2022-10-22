<?php

class CQC_Testing_Model_Queue extends CQC_Testing_AbstractModel {
    protected $table = 'queue';

    protected $fillable = [
        'test_id',
    ];

    /**
     * Test relation.
     *
     * @return \CModel_Relation_BelongsTo
     */
    public function test() {
        return $this->belongsTo(CQC_Testing_Model_Test::class);
    }
}
