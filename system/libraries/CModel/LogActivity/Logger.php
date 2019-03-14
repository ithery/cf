<?php

/**
 * 
 */
class CModel_LogActivity_Logger
{
    /** @var CModel_LogActivity_ContractInterface */
    protected $activity;

    public function __construct()
    {

    }

    public function performedOn(CModel $model)
    {
        $this->getActivity()->subject()->associate($model);

        return $this;
    }

    public function on(CModel $model)
    {
        return $this->performedOn($model);
    }

    public function causedBy($modelOrId)
    {
        if ($modelOrId === null) {
            return $this;
        }

        $model = $this->normalizeCauser($modelOrId);

        $this->getActivity()->causer()->associate($model);

        return $this;
    }

    public function by($modelOrId)
    {
        return $this->causedBy($modelOrId);
    }

    public function withProperties($properties)
    {
        $this->getActivity()->properties = collect($properties);

        return $this;
    }

    public function withProperty(string $key, $value)
    {
        $this->getActivity()->properties = $this->getActivity()->properties->put($key, $value);

        return $this;
    }

    public function useLog(string $logName)
    {
        $this->getActivity()->log_name = $logName;

        return $this;
    }

    public function inLog(string $logName)
    {
        return $this->useLog($logName);
    }

    public function tap(callable $callback, string $eventName = null)
    {
        call_user_func($callback, $this->getActivity(), $eventName);

        return $this;
    }

    public function log(string $description)
    {
        $activity = $this->activity;

        $activity->description = $this->replacePlaceholders($description, $activity);

        $activity->save();

        $this->activity = null;

        return $activity;
    }

    /**
     * [normalizeCauser description]
     *
     * @method normalizeCauser
     *
     * @param  [type]          $modelOrId [description]
     *
     * @return CModel                     [description]
     */
    protected function normalizeCauser($modelOrId) 
    {
        if ($modelOrId instanceof CModel) {
            return $modelOrId;
        }

        $model = (new CModel_LogActivity_Model_Activity())->find($modelOrId) ?: null;

        if ($model instanceof CModel) {
            return $model;
        }

        throw CModel_LogActivity_Exception_CouldNotLogActivityException::couldNotDetermineUser($modelOrId);
    }

    /**
     * [replacePlaceholders description]
     *
     * @method replacePlaceholders
     *
     * @param  string              $description [description]
     * @param  CModel_LogActivity_ContractInterface    $activity    [description]
     *
     * @return string                           [description]
     */
    protected function replacePlaceholders(string $description, CModel_LogActivity_ContractInterface $activity)
    {
        return preg_replace_callback('/:[a-z0-9._-]+/i', function ($match) use ($activity) {
            $match = $match[0];

            $attribute = (string) cstr::between(':', '.', $match);

            if (! in_array($attribute, ['subject', 'causer', 'properties'])) {
                return $match;
            }

            $propertyName = substr($match, strpos($match, '.') + 1);

            $attributeValue = $activity->$attribute;

            if (is_null($attributeValue)) {
                return $match;
            }

            $attributeValue = $attributeValue->toArray();

            return carr::get($attributeValue, $propertyName, $match);
        }, $description);
    }

    /**
     * '[getActivity description]'
     *
     * @method getActivity
     *
     * @return CModel_LogActivity_ContractInterface      [description]
     */
    protected function getActivity()
    {
        if (! $this->activity instanceof CModel_LogActivity_ContractInterface) {
            $this->activity = new CModel_LogActivity_Model_Activity();
            $this
                ->useLog($this->defaultLogName)
                ->withProperties([])
                ->causedBy($this->auth->guard($this->authDriver)->user());
        }

        return $this->activity;
    }

    /**
     * [activity description]
     *
     * @method activity
     *
     * @param  string|null $logName [description]
     *
     * @return CModel_LogActivity_Logger               [description]
     */
    public static function activity(string $logName = null)
    {
        // $defaultLogName = config('activitylog.default_log_name');

        // $logStatus = app(ActivityLogStatus::class); new ActivityLogStatus();

        return new static;

        // return app(ActivityLogger::class)
        //     ->useLog($logName ?? $defaultLogName)
        //     ->setLogStatus($logStatus);
    }
}
