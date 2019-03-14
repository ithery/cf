<?php

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface CModel_LogActivity_ContractInterface
{
	/**
	 * [subject description]
	 *
	 * @method subject
	 *
	 * @return Illuminate\Database\Eloquent\Relations\MorphTo  [description]
	 */
    public function subject();

    /**
     * [causer description]
     *
     * @method causer
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo [description]
     */
    public function causer();

    public function getExtraProperty(string $propertyName);

    /**
     * [changes description]
     *
     * @method changes
     *
     * @return CModel_Collection  [description]
     */
    public function changes();

    /**
     * [scopeInLog description]
     *
     * @method scopeInLog
     *
     * @param  CDatabase_Query_Builder    $query    [description]
     * @param  [type]     $logNames [description]
     *
     * @return CDatabase_Query_Builder               [description]
     */
    public function scopeInLog(CDatabase_Query_Builder $query, ...$logNames);

    /**
     * [scopeCausedBy description]
     *
     * @method scopeCausedBy
     *
     * @param  CDatabase_Query_Builder       $query  [description]
     * @param  CModel        $causer [description]
     *
     * @return CDatabase_Query_Builder                [description]
     */
    public function scopeCausedBy(CDatabase_Query_Builder $query, CModel $causer);

    /**
     * [scopeForSubject description]
     *
     * @method scopeForSubject
     *
     * @param  CDatabase_Query_Builder         $query   [description]
     * @param  CModel          $subject [description]
     *
     * @return CDatabase_Query_Builder                   [description]
     */
    public function scopeForSubject(CDatabase_Query_Builder $query, CModel $subject);
}
