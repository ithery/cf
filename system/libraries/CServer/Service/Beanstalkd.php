<?php

use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Job as PheanstalkJob;

class CServer_Service_Beanstalkd {
    /**
     * @var null|Pheanstalk
     */
    protected $client;

    /**
     * @var null|CServer_Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @param null|CServer_Server|array $server
     * @param array                     $options
     */
    public function __construct($server = null, $options = []) {
        if (is_array($server)) {
            $options = $server;
            $server = null;
        }
        $this->server = $server;
        $this->host = carr::get($options, 'host', 'localhost');
        $this->port = carr::get($options, 'port', Pheanstalk::DEFAULT_PORT);

        if ($this->server === null || $this->server->isLocal()) {
            $timeout = carr::get($options, 'timeout', Connection::DEFAULT_CONNECT_TIMEOUT);
            $this->client = Pheanstalk::create($this->host, $this->port, $timeout);
        }
    }

    /**
     * @return bool
     */
    protected function isRemote() {
        return $this->client === null && $this->server !== null && $this->server->isRemote();
    }

    /**
     * @return array
     */
    public function getTubes() {
        if (!$this->isRemote()) {
            $tubes = $this->client->listTubes();
            sort($tubes);

            return $tubes;
        }

        $output = $this->sendRawCommand('list-tubes');
        $tubes = $this->parseListOutput($output);
        sort($tubes);

        return $tubes;
    }

    public function getTubesStats() {
        $stats = [];
        foreach ($this->getTubes() as $tube) {
            $stats[] = $this->getTubeStats($tube);
        }

        return $stats;
    }

    /**
     * @param string $tube
     *
     * @return array
     */
    public function getRawTubeStats($tube) {
        if (!$this->isRemote()) {
            return $this->client->statsTube($tube);
        }

        $output = $this->sendRawCommand('stats-tube ' . $tube);

        return $this->parseStatsOutput($output);
    }

    /**
     * @param string $tube
     *
     * @return array
     */
    public function getTubeStats($tube) {
        $stats = [];
        $descr = [
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
            'pause-time-left' => 'the number of seconds until the tube is un-paused'];
        $nameTube = [
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
            'pause-time-left' => 'Pause(left)'];

        $rawStats = $this->getRawTubeStats($tube);
        foreach ($rawStats as $key => $value) {
            if (!array_key_exists($key, $nameTube)) {
                continue;
            }
            $stats[] = [
                'key' => $nameTube[$key],
                'value' => $value,
                'descr' => isset($descr[$key]) ? $descr[$key] : ''];
        }

        return $stats;
    }

    public static function getServerStatsFields() {
        return [
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
        ];
    }

    /**
     * @return array
     */
    public function getServerStats() {
        $fields = $this->getServerStatsFields();
        $stats = [];

        if (!$this->isRemote()) {
            $object = $this->client->stats();
        } else {
            $output = $this->sendRawCommand('stats');
            $object = $this->parseStatsOutput($output);
        }

        foreach ($fields as $key => $description) {
            if (isset($object[$key])) {
                $stats[$key] = [
                    'key' => $key,
                    'description' => $description,
                    'value' => $object[$key],
                ];
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
        return [
            'ready' => $this->peekReady($tube),
            'delayed' => $this->peekDelayed($tube),
            'buried' => $this->peekBuried($tube)];
    }

    /**
     * @param string $tube
     * @param int    $limit
     *
     * @return void
     */
    public function kick($tube, $limit) {
        if ($this->isRemote()) {
            $this->sendRawCommand('use ' . $tube);
            $this->sendRawCommand('kick ' . $limit);

            return;
        }
        $this->client->useTube($tube)->kick($limit);
    }

    /**
     * @param string $tube
     *
     * @return bool
     */
    public function deleteReady($tube) {
        if ($this->isRemote()) {
            return $this->remoteDeletePeek($tube, 'peek-ready');
        }
        $job = $this->client->useTube($tube)->peekReady();
        if ($job) {
            $this->client->delete($job);

            return true;
        }

        return false;
    }

    /**
     * @param string $tube
     *
     * @return bool
     */
    public function deleteBuried($tube) {
        if ($this->isRemote()) {
            return $this->remoteDeletePeek($tube, 'peek-buried');
        }
        $job = $this->client->useTube($tube)->peekBuried();
        if ($job) {
            $this->client->delete($job);

            return true;
        }

        return false;
    }

    /**
     * @param string $tube
     *
     * @return bool
     */
    public function deleteDelayed($tube) {
        if ($this->isRemote()) {
            return $this->remoteDeletePeek($tube, 'peek-delayed');
        }
        $job = $this->client->useTube($tube)->peekDelayed();
        if ($job) {
            $this->client->delete($job);

            return true;
        }

        return false;
    }

    /**
     * @param string $tube
     * @param int    $delay
     *
     * @return void
     */
    public function pauseTube($tube, $delay) {
        if ($this->isRemote()) {
            $this->sendRawCommand('pause-tube ' . $tube . ' ' . $delay);

            return;
        }
        $this->client->pauseTube($tube, $delay);
    }

    public function getContentType() {
        return $this->contentType;
    }

    /**
     * @param string $tubeName
     * @param string $tubeData
     * @param int    $tubePriority
     * @param int    $tubeDelay
     * @param int    $tubeTtr
     *
     * @return mixed
     */
    public function addJob($tubeName, $tubeData, $tubePriority = Pheanstalk::DEFAULT_PRIORITY, $tubeDelay = Pheanstalk::DEFAULT_DELAY, $tubeTtr = Pheanstalk::DEFAULT_TTR) {
        if ($this->isRemote()) {
            throw new Exception('addJob is not supported on remote server');
        }
        $this->client->useTube($tubeName);
        $result = $this->client->useTube($tubeName)->put($tubeData, $tubePriority, $tubeDelay, $tubeTtr);

        return $result;
    }

    /**
     * @param string $tube
     * @param string $method
     * @param bool   $autoDecode
     *
     * @return array
     */
    private function peek($tube, $method, $autoDecode = false) {
        if ($this->isRemote()) {
            $peekTypeMap = [
                'peekReady' => 'peek-ready',
                'peekDelayed' => 'peek-delayed',
                'peekBuried' => 'peek-buried',
            ];

            return $this->remotePeek($tube, carr::get($peekTypeMap, $method, $method));
        }

        $peek = [];

        try {
            $job = $this->client->useTube($tube)->{$method}();
            if ($job) {
                $peek = [
                    'id' => $job->getId(),
                    'rawData' => $job->getData(),
                    'data' => $job->getData(),
                    'stats' => $this->client->statsJob($job)
                ];
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        if ($peek && $autoDecode) {
            $peek['data'] = $this->decodeData($peek['data']);
        }

        return $peek;
    }

    /**
     * @param string $pData
     *
     * @return mixed
     */
    private function decodeData($pData) {
        $this->contentType = '';
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
            $this->contentType = 'php';
            $out = $data;
        } else {
            $data = @json_decode($pData, true);

            if ($data) {
                $this->contentType = 'json';
                $out = $data;
            }
        }

        return $out;
    }

    /**
     * @param string $command
     *
     * @return string
     */
    private function sendRawCommand($command) {
        $escapedCmd = addcslashes($command, '"\\');
        $phpCode = implode('', [
            '$f=@fsockopen("' . $this->host . '",' . $this->port . ',$e,$m,5);',
            'if(!$f){echo "CONNECT_ERROR:".$m;exit(1);}',
            'fwrite($f,"' . $escapedCmd . '\\r\\n");',
            'stream_set_timeout($f,2);',
            '$h=fgets($f);',
            'if(strpos($h,"OK")===0){$n=(int)substr($h,3);echo fread($f,$n);}',
            'else{echo trim($h);}',
            'fclose($f);',
        ]);

        return trim($this->server->runCommand("php -r '" . $phpCode . "'"));
    }

    /**
     * @param string $tube
     * @param string $peekType
     *
     * @return array
     */
    private function remotePeek($tube, $peekType) {
        $escapedTube = addcslashes($tube, '"\\');
        $phpCode = implode('', [
            '$f=@fsockopen("' . $this->host . '",' . $this->port . ',$e,$m,5);',
            'if(!$f){echo "{}";exit(1);}',
            'fwrite($f,"use ' . $escapedTube . '\\r\\n");',
            'fgets($f);',
            'fwrite($f,"' . $peekType . '\\r\\n");',
            '$h=trim(fgets($f));',
            'if(strpos($h,"FOUND")!==0){echo "{}";fclose($f);exit;}',
            '$p=explode(" ",$h);$id=$p[1];$n=(int)$p[2];',
            '$data="";$r=$n+2;',
            'while(strlen($data)<$r){$c=fread($f,$r-strlen($data));if($c===false)break;$data.=$c;}',
            '$data=substr($data,0,$n);',
            'fwrite($f,"stats-job ".$id."\\r\\n");',
            '$sh=fgets($f);$stats="";',
            'if(strpos($sh,"OK")===0){$sn=(int)substr($sh,3);while(strlen($stats)<$sn){$c=fread($f,$sn-strlen($stats));if($c===false)break;$stats.=$c;}}',
            'fclose($f);',
            'echo json_encode(["id"=>(int)$id,"data"=>$data,"stats"=>$stats]);',
        ]);

        $output = trim($this->server->runCommand("php -r '" . $phpCode . "'"));
        $result = json_decode($output, true);
        if (!is_array($result) || !isset($result['id'])) {
            return [];
        }

        return [
            'id' => $result['id'],
            'rawData' => $result['data'],
            'data' => $result['data'],
            'stats' => $this->parseStatsOutput(carr::get($result, 'stats', '')),
        ];
    }

    /**
     * @param string $tube
     * @param string $peekType
     *
     * @return bool
     */
    private function remoteDeletePeek($tube, $peekType) {
        $escapedTube = addcslashes($tube, '"\\');
        $phpCode = implode('', [
            '$f=@fsockopen("' . $this->host . '",' . $this->port . ',$e,$m,5);',
            'if(!$f)exit(1);',
            'fwrite($f,"use ' . $escapedTube . '\\r\\n");',
            'fgets($f);',
            'fwrite($f,"' . $peekType . '\\r\\n");',
            '$h=trim(fgets($f));',
            'if(strpos($h,"FOUND")!==0){echo "0";fclose($f);exit;}',
            '$p=explode(" ",$h);$id=$p[1];$n=(int)$p[2];',
            '$data="";$r=$n+2;',
            'while(strlen($data)<$r){$c=fread($f,$r-strlen($data));if($c===false)break;$data.=$c;}',
            'fwrite($f,"delete ".$id."\\r\\n");',
            '$dh=trim(fgets($f));',
            'fclose($f);',
            'echo $dh==="DELETED"?"1":"0";',
        ]);

        $output = trim($this->server->runCommand("php -r '" . $phpCode . "'"));

        return $output === '1';
    }

    /**
     * @param string $output
     *
     * @return array
     */
    private function parseStatsOutput($output) {
        $result = [];
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line === '---') {
                continue;
            }
            if (strpos($line, ': ') !== false) {
                list($key, $value) = explode(': ', $line, 2);
                $result[trim($key)] = trim($value);
            }
        }

        return $result;
    }

    /**
     * @param string $output
     *
     * @return array
     */
    private function parseListOutput($output) {
        $result = [];
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line === '---') {
                continue;
            }
            if (strpos($line, '- ') === 0) {
                $result[] = substr($line, 2);
            }
        }

        return $result;
    }
}
