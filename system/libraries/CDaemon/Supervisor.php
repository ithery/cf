<?php

class CDaemon_Supervisor {
    /**
     * @var CDaemon_Supervisor_Contract_SupervisorCommandQueueInterface
     */
    protected static $supervisorCommandQueue;

    /**
     * @var CDaemon_Supervisor_Contract_SupervisorRepositoryInterface
     */
    protected static $supervisorRepository;

    /**
     * @var CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface
     */
    protected static $masterSupervisorRepository;

    /**
     * @var CDaemon_Supervisor_AutoScaler
     */
    protected static $autoScaler;

    /**
     * @var CDaemon_Supervisor_Contract_MetricsRepositoryInterface
     */
    protected static $metricsRepository;

    /**
     * @var CDaemon_Supervisor_Contract_TagRepositoryInterface
     */
    protected static $tagRepository;

    /**
     * @var CDaemon_Supervisor_Contract_ProcessRepositoryInterface
     */
    protected static $processRepository;

    /**
     * @var CDaemon_Supervisor_Contract_WorkloadRepositoryInterface
     */
    protected static $workloadRepository;

    /**
     * @var CDaemon_Supervisor_WaitTimeCalculator
     */
    protected static $waitTimeCalculator;

    /**
     * @var CDaemon_Supervisor_RedisLock
     */
    protected static $lock;

    /**
     * @var CDaemon_Supervisor_SystemProcessCounter
     */
    protected static $systemProcessCounter;

    /**
     * @var CDaemon_Supervisor_Repository_RedisJobRepository
     */
    protected static $jobRepository;

    /**
     * @var CDaemon_Supervisor_Stopwatch
     */
    protected static $stopwatch;

    public static function supervisorCommandQueue() {
        if (static::$supervisorCommandQueue == null) {
            static::$supervisorCommandQueue = new CDaemon_Supervisor_RedisSupervisorCommandQueue();
        }

        return static::$supervisorCommandQueue;
    }

    /**
     * @return CDaemon_Supervisor_Contract_SupervisorRepositoryInterface
     */
    public static function supervisorRepository() {
        if (static::$supervisorRepository == null) {
            static::$supervisorRepository = new CDaemon_Supervisor_Repository_RedisSupervisorRepository();
        }

        return static::$supervisorRepository;
    }

    /**
     * @return CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface
     */
    public static function masterSupervisorRepository() {
        if (static::$masterSupervisorRepository == null) {
            static::$masterSupervisorRepository = new CDaemon_Supervisor_Repository_RedisMasterSupervisorRepository();
        }

        return static::$masterSupervisorRepository;
    }

    /**
     * @return CDaemon_Supervisor_Contract_MetricsRepositoryInterface
     */
    public static function metricsRepository() {
        if (static::$metricsRepository == null) {
            static::$metricsRepository = new CDaemon_Supervisor_Repository_RedisMetricsRepository();
        }

        return static::$metricsRepository;
    }

    /**
     * @return CDaemon_Supervisor_Contract_TagRepositoryInterface
     */
    public static function tagRepository() {
        if (static::$tagRepository == null) {
            static::$tagRepository = new CDaemon_Supervisor_Repository_RedisTagRepository();
        }

        return static::$tagRepository;
    }

    /**
     * @return CDaemon_Supervisor_Contract_ProcessRepositoryInterface
     */
    public static function processRepository() {
        if (static::$processRepository == null) {
            static::$processRepository = new CDaemon_Supervisor_Repository_RedisProcessRepository();
        }

        return static::$processRepository;
    }

    /**
     * @return CDaemon_Supervisor_Contract_WorkloadRepositoryInterface
     */
    public static function workloadRepository() {
        if (static::$workloadRepository == null) {
            static::$workloadRepository = new CDaemon_Supervisor_Repository_RedisWorkloadRepository();
        }

        return static::$workloadRepository;
    }

    /**
     * @return CDaemon_Supervisor_AutoScaler
     */
    public static function autoScaler() {
        if (static::$autoScaler == null) {
            static::$autoScaler = new CDaemon_Supervisor_AutoScaler();
        }

        return static::$autoScaler;
    }

    /**
     * @return CDaemon_Supervisor_WaitTimeCalculator
     */
    public static function waitTimeCalculator() {
        if (static::$waitTimeCalculator == null) {
            static::$waitTimeCalculator = new CDaemon_Supervisor_WaitTimeCalculator();
        }

        return static::$waitTimeCalculator;
    }

    /**
     * @return CDaemon_Supervisor_RedisLock
     */
    public static function lock() {
        if (static::$lock == null) {
            static::$lock = new CDaemon_Supervisor_RedisLock();
        }

        return static::$lock;
    }

    /**
     * @return CDaemon_Supervisor_SystemProcessCounter
     */
    public static function systemProcessCounter() {
        if (static::$systemProcessCounter == null) {
            static::$systemProcessCounter = new CDaemon_Supervisor_SystemProcessCounter();
        }

        return static::$systemProcessCounter;
    }

    /**
     * @return CDaemon_Supervisor_Repository_RedisJobRepository
     */
    public static function jobRepository() {
        if (static::$jobRepository == null) {
            static::$jobRepository = new CDaemon_Supervisor_Repository_RedisJobRepository();
        }

        return static::$jobRepository;
    }

    /**
     * @return CDaemon_Supervisor_Stopwatch
     */
    public static function stopwatch() {
        if (static::$stopwatch == null) {
            static::$stopwatch = new CDaemon_Supervisor_Stopwatch();
        }

        return static::$stopwatch;
    }

    public static function totalPausedMasters() {
        if (!$masters = static::masterSupervisorRepository()->all()) {
            return 0;
        }

        return c::collect($masters)->filter(function ($master) {
            return $master->status === 'paused';
        })->count();
    }

    public static function totalProcessCount() {
        $supervisors = static::supervisorRepository()->all();

        return c::collect($supervisors)->reduce(function ($carry, $supervisor) {
            return $carry + c::collect($supervisor->processes)->sum();
        }, 0);
    }

    /**
     * Get the current status of Horizon.
     *
     * @return string
     */
    public static function currentStatus() {
        if (!$masters = static::masterSupervisorRepository()->all()) {
            return 'inactive';
        }

        return c::collect($masters)->every(function ($master) {
            return $master->status === 'paused';
        }) ? 'paused' : 'running';
    }

    /**
     * @return CDaemon_Supervisor_Manager
     */
    public static function manager() {
        return CDaemon_Supervisor_Manager::instance();
    }

    public static function boot() {
        CDaemon_Supervisor_Bootstrap::boot();
    }
}
