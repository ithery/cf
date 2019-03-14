<?php

use Illuminate\Database\Eloquent\Relations\MorphTo;

class CModel_LogActivity_Model_Activity extends CModel implements CModel_LogActivity_ContractInterface
{
    protected $table = 'log_activity';
    public $guarded = ['log_activity_id'];

    protected $casts = [
        'properties' => 'collection',
    ];

    /**
     * [subject description]
     *
     * @method subject
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo  [description]
     */
    public function subject($withTrashed = false)
    {
        if ($withTrashed) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    /**
     * [causer description]
     *
     * @method causer
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo [description]
     */
    public function causer()
    {
        return $this->morphTo();
    }

    public function getExtraProperty(string $propertyName)
    {
        return carr::get($this->properties->toArray(), $propertyName);
    }

    /**
     * [changes description]
     *
     * @method changes
     *
     * @return CModel_Collection  [description]
     */
    public function changes()
    {
        if (! $this->properties instanceof CModel_Collection) {
            return new CModel_Collection();
        }

        return $this->properties->only(['attributes', 'old']);
    }

    /**
     * [getChangesAttribute description]
     *
     * @method getChangesAttribute
     *
     * @return CModel_Collection              [description]
     */
    public function getChangesAttribute()
    {
        return $this->changes();
    }

    /**
     * [scopeInLog description]
     *
     * @method scopeInLog
     *
     * @param  CDatabase_Query_Builder $query    [description]
     * @param  [type]                  $logNames [description]
     *
     * @return CDatabase_Query_Builder                            [description]
     */
    public function scopeInLog(CDatabase_Query_Builder $query, ...$logNames)
    {
        if (is_array($logNames[0])) {
            $logNames = $logNames[0];
        }

        return $query->whereIn('log_name', $logNames);
    }

    /**
     * [scopeCausedBy description]
     *
     * @method scopeCausedBy
     *
     * @param  CDatabase_Query_Builder $query  [description]
     * @param  CModel                  $causer [description]
     *
     * @return CDatabase_Query_Builder                          [description]
     */
    public function scopeCausedBy(CDatabase_Query_Builder $query, CModel $causer)
    {
        return $query
            ->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }

    /**
     * [scopeForSubject description]
     *
     * @method scopeForSubject
     *
     * @param  CDatabase_Query_Builder $query   [description]
     * @param  CModel                  $subject [description]
     *
     * @return CDatabase_Query_Builder                           [description]
     */
    public function scopeForSubject(CDatabase_Query_Builder $query, CModel $subject)
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }
}
