<?php


use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CModel_LogActivity_Trait_LogsActivity
{
    use CModel_LogActivity_Trait_DetectsChanges;

    protected $enableLoggingModelsEvents = true;

    protected static function bootLogsActivity()
    {
        static::eventsToBeRecorded()->each(function ($eventName) {
            return static::$eventName(function (CModel $model) use ($eventName) {
                if (! $model->shouldLogEvent($eventName)) {
                    return;
                }

                $description = $model->getDescriptionForEvent($eventName);

                $logName = $model->getLogNameToUse($eventName);

                if ($description == '') {
                    return;
                }

                $logger = (new CModel_LogActivity_Logger())
                    ->performedOn($model)
                    ->withProperties($model->attributeValuesToBeLogged($eventName));

                // $logger = app(ActivityLogger::class)
                //     ->useLog($logName)
                //     ->performedOn($model)
                //     ->withProperties($model->attributeValuesToBeLogged($eventName));

                if (method_exists($model, 'tapActivity')) {
                    $logger->tap([$model, 'tapActivity'], $eventName);
                }

                $logger->log($description);
            });
        });
    }

    public function disableLogging()
    {
        $this->enableLoggingModelsEvents = false;

        return $this;
    }

    public function enableLogging()
    {
        $this->enableLoggingModelsEvents = true;

        return $this;
    }

    /**
     * [activities description]
     *
     * @method activities
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany     [description]
     */
    public function activities()
    {
        return $this->morphMany('CModel_LogActivity_Model_Activity', 'subject');
    }

    /**
     * [getDescriptionForEvent description]
     *
     * @method getDescriptionForEvent
     *
     * @param  string                 $eventName [description]
     *
     * @return string                            [description]
     */
    public function getDescriptionForEvent($eventName)
    {
        return $eventName;
    }

    /**
     * [getLogNameToUse description]
     *
     * @method getLogNameToUse
     *
     * @param  string          $eventName [description]
     *
     * @return string                     [description]
     */
    public function getLogNameToUse($eventName = '')
    {
        if (isset(static::$logName)) {
            return static::$logName;
        }

        return 'default';
    }

    /**
     * Get the event names that should be recorded.
     *
     * @method eventsToBeRecorded
     *
     * @return CModel_Collection             [description]
     */
    protected static function eventsToBeRecorded()
    {
        if (isset(static::$recordEvents)) {
            return collect(static::$recordEvents);
        }

        $events = collect([
            'created',
            'updated',
            'deleted',
        ]);

        if (collect(class_uses_recursive(static::class))->contains('CModel_SoftDelete_Trait')) {
            $events->push('restored');
        }

        return $events;
    }

    /**
     * [attributesToBeIgnored description]
     *
     * @method attributesToBeIgnored
     *
     * @return array                [description]
     */
    public function attributesToBeIgnored()
    {
        if (! isset(static::$ignoreChangedAttributes)) {
            return [];
        }

        return static::$ignoreChangedAttributes;
    }

    /**
     * [shouldLogEvent description]
     *
     * @method shouldLogEvent
     *
     * @param  string         $eventName [description]
     *
     * @return bool                    [description]
     */
    protected function shouldLogEvent(string $eventName)
    {
        if (! $this->enableLoggingModelsEvents) {
            return false;
        }

        if (! in_array($eventName, ['created', 'updated'])) {
            return true;
        }

        if (carr::has($this->getDirty(), 'deleted_at')) {
            if ($this->getDirty()['deleted_at'] === null) {
                return false;
            }
        }

        //do not log update event if only ignored attributes are changed
        return (bool) count(carr::except($this->getDirty(), $this->attributesToBeIgnored()));
    }
}
