<?php

class CDaemon_Supervisor_Queue_Tags {
    /**
     * Determine the tags for the given job.
     *
     * @param mixed $job
     *
     * @return array
     */
    public static function for($job) {
        if ($tags = static::extractExplicitTags($job)) {
            return $tags;
        }

        return static::modelsFor(static::targetsFor($job))->map(function ($model) {
            return get_class($model) . ':' . $model->getKey();
        })->all();
    }

    /**
     * Extract tags from job object.
     *
     * @param mixed $job
     *
     * @return array
     */
    public static function extractExplicitTags($job) {
        return $job instanceof CEvent_CallQueuedListener
                    ? static::tagsForListener($job)
                    : static::explicitTags(static::targetsFor($job));
    }

    /**
     * Determine tags for the given queued listener.
     *
     * @param mixed $job
     *
     * @return array
     */
    protected static function tagsForListener($job) {
        return c::collect(
            [static::extractListener($job), static::extractEvent($job)]
        )->map(function ($job) {
            return static::for($job);
        })->collapse()->unique()->toArray();
    }

    /**
     * Determine tags for the given job.
     *
     * @param array $jobs
     *
     * @return mixed
     */
    protected static function explicitTags(array $jobs) {
        return c::collect($jobs)->map(function ($job) {
            return method_exists($job, 'tags') ? $job->tags() : [];
        })->collapse()->unique()->all();
    }

    /**
     * Get the actual target for the given job.
     *
     * @param mixed $job
     *
     * @return array
     */
    public static function targetsFor($job) {
        if ($job instanceof CBroadcast_BroadcastEvent) {
            return [$job->event];
        }
        if ($job instanceof CEvent_CallQueuedListener) {
            return [static::extractEvent($job)];
        }

        if ($job instanceof CEmail_TaskQueue_SendQueuedMailable) {
            return [$job->mailable];
        }

        // if ($job instanceof CNotification_TaskQueue_NotificationSender) {
        //     return [$job->notification];
        // }

        return [$job];
    }

    /**
     * Get the models from the given object.
     *
     * @param array $targets
     *
     * @return \CCollection
     */
    public static function modelsFor(array $targets) {
        $models = [];

        foreach ($targets as $target) {
            $models[] = c::collect(
                (new ReflectionClass($target))->getProperties()
            )->map(function ($property) use ($target) {
                $property->setAccessible(true);

                $value = static::getValue($property, $target);

                if ($value instanceof CModel) {
                    return [$value];
                } elseif ($value instanceof CModel_Collection) {
                    return $value->all();
                }
            })->collapse()->filter()->all();
        }

        return c::collect($models)->collapse()->unique();
    }

    /**
     * Get the value of the given ReflectionProperty.
     *
     * @param \ReflectionProperty $property
     * @param mixed               $target
     */
    protected static function getValue(ReflectionProperty $property, $target) {
        if (method_exists($property, 'isInitialized')
            && !$property->isInitialized($target)
        ) {
            return;
        }

        return $property->getValue($target);
    }

    /**
     * Extract the listener from a queued job.
     *
     * @param mixed $job
     *
     * @return mixed
     */
    protected static function extractListener($job) {
        return (new ReflectionClass($job->class))->newInstanceWithoutConstructor();
    }

    /**
     * Extract the event from a queued job.
     *
     * @param mixed $job
     *
     * @return mixed
     */
    protected static function extractEvent($job) {
        return isset($job->data[0]) && is_object($job->data[0])
            ? $job->data[0]
            : new stdClass();
    }
}
