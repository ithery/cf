<?php
use Carbon\CarbonInterval;

class CDaemon_Manager {
    public static function getServiceName($className) {
        $serviceName = $className;
        $serviceNameExploded = explode('_', $className);
        if ($serviceNameExploded > 0) {
            $serviceName = carr::get($serviceNameExploded, count($serviceNameExploded) - 1);
        }

        return $serviceName;
    }

    /**
     * Create Service Object From Given Service Class Name.
     *
     * @param string $className
     *
     * @return CDaemon_ServiceAbstract
     */
    public static function createService($className) {
        $config = [];
        //get last suffix class
        $serviceName = static::getServiceName($className);
        $pidFile = CDaemon_Helper::getPidFile($className);
        $logFile = CDaemon_Helper::getLogFile($className);
        $dirPidFile = dirname($pidFile);
        if (!CFile::isDirectory($dirPidFile)) {
            CFile::makeDirectory($dirPidFile, 0755, true);
        }
        $dirLogFile = dirname($logFile);
        if (!CFile::isDirectory($dirLogFile)) {
            CFile::makeDirectory($dirLogFile, 0755, true);
        }

        $config['pidFile'] = $pidFile;
        $config['logFile'] = $logFile;
        $config['serviceClass'] = $className;
        //get last suffix class
        $serviceName = static::getServiceName($className);
        $config['serviceName'] = $serviceName;
        $config['pidFile'] = $pidFile;
        $config['logFile'] = $logFile;
        $config['stdout'] = false;
        $service = new $className($serviceName, $config);

        return $service;
    }

    /**
     * Create Service Object From Given Service Class Name.
     *
     * @param string $name
     * @param array  $config
     *
     * @return CDaemon_Supervisor_Supervisor
     */
    public static function createSupervisor($name, $config) {
        $connection = carr::get($config, 'connection');
        $queue = carr::get($config, 'queue', CF::config('queue.connections.' . $connection . '.queue', 'default'));

        $backoff = carr::get($config, 'backoff', carr::get($config, 'delay'));

        $balance = carr::get($config, 'balance');

        $tries = carr::get($config, 'tries', carr::get($config, 'maxTries', 1));

        $autoScalingStrategy = $balance === 'auto' ? carr::get($config, 'autoScalingStrategy') : null;

        $supervisorOptions = new CDaemon_Supervisor_SupervisorOptions(
            $name,
            $connection,
            $queue,
            carr::get($config, 'workersName'),
            $balance,
            $backoff,
            carr::get($config, 'maxTime'),
            carr::get($config, 'maxJobs'),
            carr::get($config, 'maxProcesses'),
            carr::get($config, 'minProcesses'),
            carr::get($config, 'memory'),
            carr::get($config, 'timeout'),
            carr::get($config, 'sleep'),
            $tries,
            carr::get($config, 'force'),
            carr::get($config, 'nice'),
            carr::get($config, 'balanceCooldown'),
            carr::get($config, 'balanceMaxShift'),
            carr::get($config, 'parentId'),
            carr::get($config, 'rest'),
            $autoScalingStrategy
        );
        $supervisor = CDaemon_Supervisor_SupervisorFactory::make($supervisorOptions);

        return $supervisor;
    }

    /**
     * Create Worker Object From Given Connection and config.
     *
     * @param string $connection
     * @param array  $config
     *
     * @return CQueue_Worker
     */
    public static function runWorker($connection, $config) {
        $name = carr::get($config, 'name');
        $queue = carr::get($config, 'queue', CF::config('queue.connections.' . $connection . '.queue', 'default'));

        $backoff = carr::get($config, 'backoff', carr::get($config, 'delay'));
        $tries = carr::get($config, 'tries', carr::get($config, 'maxTries', 1));
        $workerOptions = new CQueue_WorkerOptions(
            $name,
            $backoff,
            carr::get($config, 'memory'),
            carr::get($config, 'timeout'),
            carr::get($config, 'sleep'),
            $tries,
            carr::get($config, 'force'),
            carr::get($config, 'stopWhenEmpty'),
            carr::get($config, 'maxJobs'),
            carr::get($config, 'maxTime'),
            carr::get($config, 'rest'),
        );

        $worker = CQueue::worker();

        $output = new Symfony\Component\Console\Output\ConsoleOutput();

        $formatRunTime = function ($startTime) {
            $runTime = (microtime(true) - $startTime) * 1000;

            return $runTime > 1000
            ? CarbonInterval::milliseconds($runTime)->cascade()->forHumans()
            : number_format($runTime, 2) . 'ms';
        };
        $latestStartedAt = microtime(true);
        $logFailedJob = function (CQueue_Event_JobFailed $event) {
            CQueue_FailerFactory::getFailerInstance()->log(
                $event->connectionName,
                $event->job->getQueue(),
                $event->job->getRawBody(),
                $event->exception
            );
        };

        $writeOutput = function (CQueue_AbstractJob $job, $status, $exception = null) use ($output, $formatRunTime, &$latestStartedAt) {
            $type = $status == 'success' || $status == 'starting' ? 'success' : ($status == 'released_after_exception' ? 'warn' : 'error');
            $message = sprintf(
                "<{$type}>[%s][%s] %s</{$type}> %s %s",
                CCarbon::now()->format('Y-m-d H:i:s'),
                $job->getJobId(),
                str_pad("{$status}:", 11),
                $job->resolveName() . ($status == 'released_after_exception' ? ' Attempts:' . $job->attempts() : ''),
                $exception ? $exception->getMessage() : ''
            );
            $output->writeln($message);
        };

        CEvent::dispatcher()->listen(CQueue_Event_JobProcessing::class, function ($event) use ($writeOutput) {
            $writeOutput($event->job, 'starting');
        });

        CEvent::dispatcher()->listen(CQueue_Event_JobProcessed::class, function ($event) use ($writeOutput) {
            $writeOutput($event->job, 'success');
        });

        CEvent::dispatcher()->listen(CQueue_Event_JobReleasedAfterException::class, function (CQueue_Event_JobReleasedAfterException $event) use ($writeOutput) {
            $writeOutput($event->job, 'released_after_exception');
        });

        CEvent::dispatcher()->listen(CQueue_Event_JobFailed::class, function ($event) use ($writeOutput, $logFailedJob) {
            $writeOutput($event->job, 'failed');

            $logFailedJob($event);
        });
        CEvent::dispatcher()->listen(CQueue_Event_JobExceptionOccurred::class, function (CQueue_Event_JobExceptionOccurred $event) use ($writeOutput) {
            $writeOutput($event->job, 'error', $event->exception);
        });
        $worker->setName($name)
            ->setCache(null)
            ->daemon($connection, $queue, $workerOptions);

        return $worker;
    }
}
