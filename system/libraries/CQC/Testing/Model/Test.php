<?php
/**
 * @property      string                  $path
 * @property      string                  $name
 * @property      string                  $state
 * @property      bool                    $enabled
 * @property      int                     $suite_id
 * @property-read int                     $test_id
 * @property-read CQC_Testing_Model_Suite $suite
 */
class CQC_Testing_Model_Test extends CQC_Testing_AbstractModel {
    protected $table = 'test';

    protected $fillable = [
        'suite_id',
        'path',
        'name',
        'state',
        'sha1',
    ];

    protected $casts = [
        'enabled' => 'bool',
    ];

    /**
     * Get the full path.
     *
     * @param $value
     *
     * @return mixed|string
     */
    public function getFullPathAttribute($value) {
        return c::makePath([$this->path, $this->name]);
    }

    /**
     * Suite relation.
     *
     * @return \CModel_Relation_BelongsTo
     */
    public function suite() {
        return $this->belongsTo(CQC_Testing_Model_Suite::class);
    }

    /**
     * Get the test command.
     *
     * @param $value
     *
     * @return string
     */
    public function getTestCommandAttribute($value) {
        $command = $this->suite->testCommand;

        return $command . ' ' . $this->fullPath;
    }

    /**
     * Runs relation.
     *
     * @return \CModel_Relation_HasMany
     */
    public function runs() {
        return $this->hasMany(CQC_Testing_Model_Run::class);
    }

    /**
     * Update test sha1.
     */
    public function updateSha1() {
        $this->sha1 = @sha1_file($this->fullPath);

        $this->save();
    }

    /**
     * Check if the sha1 changed.
     *
     * @return bool
     */
    public function sha1Changed() {
        return $this->sha1 !== @sha1_file($this->fullPath);
    }

    public function getFile() {
        return c::makePath([$this->path, $this->name]);
    }
}
