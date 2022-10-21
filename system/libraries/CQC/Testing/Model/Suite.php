<?php
class CQC_Testing_Model_Suite extends CQC_Testing_AbstractModel {
    protected $table = 'suite';

    protected $fillable = [
        'name',
        'tests_path',
        'suite_path',
        'file_mask',
        'command_options',
        'max_retries',
        'editor',
        'coverage_enabled',
        'coverage_index',
    ];

    /**
     * Get the full path.
     *
     * @param $value
     *
     * @return mixed|string
     */
    public function getTestsFullPathAttribute($value) {
        return c::makePath(
            [
                $this->project->tests_full_path,
                $this->tests_path,
            ]
        );
    }

    /**
     * Tests relation.
     *
     * @return \CModel_Relation_HasMany
     */
    public function tests() {
        return $this->hasMany(CQC_Testing_Model_Test::class);
    }
}
