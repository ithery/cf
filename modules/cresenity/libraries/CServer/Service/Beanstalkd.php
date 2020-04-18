<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Job as PheanstalkJob;

class CServer_Service_Beanstalkd {

    protected $client;
    protected $contentType;

    public function __construct($options = []) {
        $host = carr::get($options, 'host', 'localhost');
        $port = carr::get($options, 'port', Pheanstalk::DEFAULT_PORT);
        $timeout = carr::get($options, 'timeout', Connection::DEFAULT_CONNECT_TIMEOUT);



        $this->client = Pheanstalk::create($host, $port, $timeout);
    }

    public function getTubes() {
        $tubes = $this->client->listTubes();
        sort($tubes);
        return $tubes;
    }

    public function getTubesStats() {
        $stats = array();
        foreach ($this->getTubes() as $tube) {
            $stats[] = $this->getTubeStats($tube);
        }
        return $stats;
    }

    public function getRawTubeStats($tube) {
        return $this->client->statsTube($tube);
    }

    public function getTubeStats($tube) {
        $stats = array();
        $descr = array(
            'name' => 'the tube\'s name',
            'current-jobs-urgent' => 'the number of ready jobs with priority < 1024 in this tube',
            'current-jobs-ready' => 'the number of jobs in the ready queue in this tube',
            'current-jobs-reserved' => 'the number of jobs reserved by all clients in this tube',
            'current-jobs-delayed' => 'the number of delayed jobs in this tube',
            'current-jobs-buried' => 'the number of buried jobs in this tube',
            'total-jobs' => 'the cumulative count of jobs created in this tube',
            'current-waiting' => 'the number of open connections that have issued a reserve command while watching this tube but not yet received a response',
            'cmd-delete' => 'the cumulative number of delete commands for this tube',
            'pause' => 'the number of seconds the tube has been paused for',
            'cmd-pause-tube' => 'the cumulative number of pause-tube commands for this tube',
            'pause-time-left' => 'the number of seconds until the tube is un-paused');
        $nameTube = array(
            'name' => 'name',
            'current-jobs-urgent' => 'Urgent',
            'current-jobs-ready' => 'Ready',
            'current-jobs-reserved' => 'Reserved',
            'current-jobs-delayed' => 'Delayed',
            'current-jobs-buried' => 'Buried',
            'total-jobs' => 'Total',
            'current-using' => 'Using',
            'current-watching' => 'Watching',
            'current-waiting' => 'Waiting',
            'cmd-delete' => 'Delete(cmd)',
            'cmd-pause-tube' => 'Pause(cmd)',
            'pause' => 'Pause(sec)',
            'pause-time-left' => 'Pause(left)');
        foreach ($this->client->statsTube($tube) as $key => $value) {
            if (!array_key_exists($key, $nameTube)) {
                continue;
            }
            $stats[] = array(
                'key' => $nameTube[$key],
                'value' => $value,
                'descr' => isset($descr[$key]) ? $descr[$key] : '');
        }
        return $stats;
    }

    public static function getServerStatsFields() {
        return array(
            'binlog-current-index' => 'the index of the current binlog file being written to. If binlog is not active this value will be 0',
            'binlog-max-size' => 'the maximum size in bytes a binlog file is allowed to get before a new binlog file is opened',
            'binlog-oldest-index' => 'the index of the oldest binlog file needed to store the current jobs',
            'binlog-records-migrated' => 'the cumulative number of records written as part of compaction',
            'binlog-records-written' => 'the cumulative number of records written to the binlog',
            'cmd-bury' => 'the cumulative number of bury commands',
            'cmd-delete' => 'the cumulative number of delete commands',
            'cmd-ignore' => 'the cumulative number of ignore commands',
            'cmd-kick' => 'the cumulative number of kick commands',
            'cmd-list-tube-used' => 'the cumulative number of list-tube-used commands',
            'cmd-list-tubes' => 'the cumulative number of list-tubes commands',
            'cmd-list-tubes-watched' => 'the cumulative number of list-tubes-watched commands',
            'cmd-pause-tube' => 'the cumulative number of pause-tube commands',
            'cmd-peek' => 'the cumulative number of peek commands',
            'cmd-peek-buried' => 'the cumulative number of peek-buried commands',
            'cmd-peek-delayed' => 'the cumulative number of peek-delayed commands',
            'cmd-peek-ready' => 'the cumulative number of peek-ready commands',
            'cmd-put' => 'the cumulative number of put commands',
            'cmd-release' => 'the cumulative number of release commands',
            'cmd-reserve' => 'the cumulative number of reserve commands',
            'cmd-stats' => 'the cumulative number of stats commands',
            'cmd-stats-job' => 'the cumulative number of stats-job commands',
            'cmd-stats-tube' => 'the cumulative number of stats-tube commands',
            'cmd-use' => 'the cumulative number of use commands',
            'cmd-watch' => 'the cumulative number of watch commands',
            'current-connections' => 'the number of currently open connections',
            'current-jobs-buried' => 'the number of buried jobs',
            'current-jobs-delayed' => 'the number of delayed jobs',
            'current-jobs-ready' => 'the number of jobs in the ready queue',
            'current-jobs-reserved' => 'the number of jobs reserved by all clients',
            'current-jobs-urgent' => 'the number of ready jobs with priority < 1024',
            'current-producers' => 'the number of open connections that have each issued at least one put command',
            'current-tubes' => 'the number of currently-existing tubes',
            'current-waiting' => 'the number of open connections that have issued a reserve command but not yet received a response',
            'current-workers' => 'the number of open connections that have each issued at least one reserve command',
            'hostname' => 'the hostname of the machine as determined by uname',
            'id' => 'a random id string for this server process, generated when each beanstalkd process starts',
            'job-timeouts' => 'the cumulative count of times a job has timed out',
            'max-job-size' => 'the maximum number of bytes in a job',
            'pid' => 'the process id of the server',
            'rusage-stime' => 'the cumulative system CPU time of this process in seconds and microseconds',
            'rusage-utime' => 'the cumulative user CPU time of this process in seconds and microseconds',
            'total-connections' => 'the cumulative count of connections',
            'total-jobs' => 'the cumulative count of jobs created',
            'uptime' => 'the number of seconds since this server process started running',
            'version' => 'the version string of the server',
        );
    }

    public function getServerStats() {
        $fields = $this->getServerStatsFields();
        $stats = array();
        $object = $this->client->stats();
        foreach ($fields as $key => $description) {
            if (isset($object[$key])) {
                $stats[$key] = array(
                    'key' => $key,
                    'description' => $description,
                    'value' => $object[$key],
                );
            }
        }
        return $stats;
    }

    public function peekReady($tube) {
        return $this->peek($tube, 'peekReady');
    }

    public function peekDelayed($tube) {
        return $this->peek($tube, 'peekDelayed');
    }

    public function peekBuried($tube) {
        return $this->peek($tube, 'peekBuried');
    }

    public function peekAll($tube) {
        return array(
            'ready' => $this->peekReady($tube),
            'delayed' => $this->peekDelayed($tube),
            'buried' => $this->peekBuried($tube));
    }

    public function kick($tube, $limit) {
        $this->client->useTube($tube)->kick($limit);
    }

    public function deleteReady($tube) {
        $job = $this->client->useTube($tube)->peekReady();
        $this->client->delete($job);
    }

    public function deleteBuried($tube) {
        $job = $this->client->useTube($tube)->peekBuried();
        $this->client->delete($job);
    }

    public function deleteDelayed($tube) {
        $job = $this->client->useTube($tube)->peekDelayed();
        $this->client->delete($job);
    }

    public function pauseTube($tube, $delay) {
        $this->client->pauseTube($tube, $delay);
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function addJob($tubeName, $tubeData, $tubePriority = Pheanstalk::DEFAULT_PRIORITY, $tubeDelay = Pheanstalk::DEFAULT_DELAY, $tubeTtr = Pheanstalk::DEFAULT_TTR) {
        $this->_client->useTube($tubeName);
        $result = $this->_client->useTube($tubeName)->put($tubeData, $tubePriority, $tubeDelay, $tubeTtr);
        return $result;
    }

    /**
     * Pheanstalk class instance
     *
     * @var Pheanstalk
     */
    private function peek($tube, $method) {
        $peek = [];
        try {
            $job = $this->client->useTube($tube)->{$method}();
            if ($job) {
                $peek = array(
                    'id' => $job->getId(),
                    'rawData' => $job->getData(),
                    'data' => $job->getData(),
                    'stats' => $this->client->statsJob($job));
            }
        } catch (Exception $ex) {
            
        }
        if ($peek) {
            $peek['data'] = $this->decodeDate($peek['data']);
        }
        return $peek;
    }

    private function decodeDate($pData) {
        $this->contentType = false;
        $out = $pData;
        $data = null;

        try {
            $data = base64_decode($pData);
        } catch (Exception $e) {
            
        }
        if (!$data) {
            try {
                $data = unserialize($pData);
            } catch (Exception $e) {
                
            }
        }
        if ($data) {
            $this->_contentType = 'php';
            $out = $data;
        } else {

            $data = @json_decode($pData, true);

            if ($data) {
                $this->_contentType = 'json';
                //$out = $data;
            }
        }
        return $out;
    }

}
