<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 4:04:42 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDaemon_Worker_Via_SysV implements CDaemon_Worker_ViaInterface, CDaemon_PluginInterface {

    /**
     * Each SHM block has a header with needed metadata.
     */
    const HEADER_ADDRESS = 1;

    /**
     * Unknown error constant
     */
    const ERROR_UNKNOWN = -1;

    /**
     * @var CDaemon_Worker_MediatorAbstract
     */
    public $mediator;

    /**
     * A handle to the IPC message queue
     * @var Resource
     */
    public $queue;

    /**
     * A handle to the IPC Shared Memory resource
     * This should be a `protected` property but in a few instances in this class closures are used in a way that
     * really makes a lot of sense and they need access. I think these issues will be fixed with the improvements
     * to $this lexical scoping in PHP5.4
     * @var Resource
     */
    public $shm;

    /**
     * How big, at any time, can the IPC shared memory allocation be.
     * Default is 5MB. Will need to be increased if you are passing large datasets as Arguments or Return values.
     * @example Allocate shared memory using $this->malloc();
     * @var float
     */
    protected $memoryAllocation;

    /**
     * Under-allocated shared memory is perhaps the largest possible cause of Worker failures, so if the Mediator believes
     * the memory is under-allocated it will set this variable and write the warning to the event log
     * @var Boolean
     */
    protected $memoryAllocationWarning = false;

    public function __construct($malloc = null) {
        if (!$malloc) {
            $malloc = 5 * 1024 * 1024;
        }
        $this->malloc($malloc);
    }

    public function __destruct() {
        unset($this->mediator);
        @shm_detach($this->shm);
        $this->shm = null;
        $this->queue = null;
    }

    /**
     * Called on Construct or Init
     * @return void
     */
    public function setup() {
        $this->setupIpc();
        if ($this->mediator->service->isParent()) {
            $this->setupShm();
        }
        if (!is_resource($this->queue)) {
            throw new Exception(__METHOD__ . " Failed. Could not attach message queue id {$this->mediator->guid}");
        }
        if (!is_resource($this->shm)) {
            throw new Exception(__METHOD__ . " Failed. Could not address shared memory block {$this->mediator->guid}");
        }
    }

    /**
     * Called on Destruct
     * @return void
     */
    public function teardown() {
        
    }

    /**
     * This is called during object construction to validate any dependencies
     * @return Array  Return array of error messages (Think stuff like "GD Library Extension Required" or "Cannot open /tmp for Writing") or an empty array
     */
    public function checkEnvironment(array $errors = array()) {
        return $errors;
    }

    /**
     * Setup the channels used by IPC -- A SysV Message Queue for message headers and a Shared Memory block for the payload.
     * @return void
     */
    private function setupIpc() {
        $this->shm = shm_attach($this->mediator->guid, $this->memoryAllocation, 0666);
        $this->queue = msg_get_queue($this->mediator->guid, 0666);
    }

    /**
     * Write and Verify the SHM header
     * @return void
     * @throws Exception
     */
    private function setupShm() {
        // Write a header to the shared memory block
        if (!shm_has_var($this->shm, self::HEADER_ADDRESS)) {
            $header = array(
                'version' => CDaemon_Worker_MediatorAbstract::VERSION,
                'memoryAllocation' => $this->memoryAllocation,
            );
            if (!shm_put_var($this->shm, self::HEADER_ADDRESS, $header))
                throw new Exception(__METHOD__ . " Failed. Could Not Read Header. If this problem persists, try manually cleaning your system's SysV Shared Memory allocations.\nYou can use built-in tools on the linux commandline or a helper script shipped with PHP Simple Daemon. ");
        }
        // Check memory allocation and warn the user if their malloc() is not actually applicable (eg they changed the malloc but used --recoverworkers)
        $header = shm_get_var($this->shm, self::HEADER_ADDRESS);
        if ($header['memoryAllocation'] <> $this->memoryAllocation)
            $this->mediator->log('Warning: Seems you\'ve using --recoverworkers after making a change to the worker malloc memory limit. To apply this change you will have to restart the daemon without the --recoverworkers option.' .
                    PHP_EOL . 'The existing memory_limit is ' . $header['memoryAllocation'] . ' bytes.');
        // If we're trying to recover previous messages/shm, scan the shared memory block for call structs and import them
        // @todo if we keep this functionality, we need to at least remove it as a CLI option implemented by CDaemon_ServiceAbstract because this will not apply to other Via conveyances
        if ($this->mediator->service->isParent() && $this->mediator->service->isRecoverWorkers()) {
            $max_id = $this->callCount;
            for ($i = 0; $i < 100000; $i++) {
                if (shm_has_var($this->shm, $i)) {
                    $o = @shm_get_var($this->shm, $i);
                    if (!is_object($o)) {
                        @shm_remove_var($this->shm, $i);
                        continue;
                    }
                    $this->mediator->set_struct($o);
                    $max_id = $i;
                }
            }
            $this->mediator->log("Starting Job Numbering at $max_id.");
            $this->callCount = $max_id;
        }
    }

    /**
     * Allocate the total size of shared memory that will be allocated for passing arguments and return values to/from the
     * worker processes. Should be sufficient to hold the working set of each worker pool.
     *
     * This is can be calculated roughly as:
     * ([Max Size Of Arguments Passed] + [Max Size of Return Value]) * ([Number of Jobs Running Concurrently] + [Number of Jobs Queued, Waiting to Run])
     *
     * The memory used by a job is freed after a worker ack's the job as complete and the onReturn handler is called.
     * The total pool of memory allocated here is freed when:
     * 1) The daemon is stopped and no messages are left in the queue.
     * 2) The daemon is restarted without the --recoverworkers flag (In this case the memory is freed and released and then re-allocated.
     *    This is useful if you need to resize the shared memory the worker uses or you just want to purge any stale messages)
     *
     * Part of the Daemon API - Use from your Daemon to allocate shared memory used among all worker processes.
     *
     * @default 1 MB
     * @param $bytes
     * @throws Exception
     * @return int
     */
    public function malloc($bytes = null) {
        if ($bytes !== null) {
            if (!is_int($bytes)) {
                throw new Exception(__METHOD__ . " Failed. Could not set SHM allocation size. Expected Integer. Given: " . gettype($bytes));
            }
            if (is_resource($this->shm)) {
                throw new Exception(__METHOD__ . " Failed. Can Not Re-Allocate SHM Size. You will have to restart the daemon without the --recoverworkers option to resize.");
            }
            $this->memoryAllocation = $bytes;
        }
        return $this->memoryAllocation;
    }

    /**
     * Puts the message on the queue
     * @param $message_type
     * @param $message
     * @return boolean
     */
    public function put(CDaemon_Worker_Call $call) {
        $that = $this;
        switch ($call->status) {
            case CDaemon_Worker_MediatorAbstract::UNCALLED:
            case CDaemon_Worker_MediatorAbstract::RETURNED:
                $encoder = function($call) use ($that) {
                    shm_put_var($that->shm, $call->id, $call);
                    return shm_has_var($that->shm, $call->id);
                };
                break;
            default:
                $encoder = function($call) {
                    return true;
                };
        }
        $error_code = null;
        if ($encoder($call))
            if (msg_send($this->queue, $call->queue(), $call->header(), true, false, $error_code))
                return true;
        if ($error_code === null)
            $error_code = self::ERROR_UNKNOWN;
        $call->errors++;
        if ($this->error($error_code, $call->errors) && $call->errors < 3) {
            $this->mediator->log("SysV::put() Failed for call_id {$call->id}: Retrying. Error Code: " . $error_code);
            return $this->put($call);
        }
        return false;
    }

    /**
     * Retrieves a message from the queue
     * @param $desired_type
     * @return CDaemon_Worker_Call
     */
    public function get($desired_type, $blocking = false) {
        $blocking = $blocking ? 0 : MSG_IPC_NOWAIT;
        $message_type = $message = $message_error = null;
        msg_receive($this->queue, $desired_type, $message_type, $this->memoryAllocation, $message, true, $blocking, $message_error);
        if (!$message) {
            $this->error($message_error);
            return false;
        }
        $that = $this;
        CDaemon::log('SysV Get Message:'.json_encode($message));
        switch ($message['status']) {
            case CDaemon_Worker_MediatorAbstract::UNCALLED:
                $decoder = function($message) use($that) {
                    $call = shm_get_var($that->shm, $message['call_id']);
                    if ($message['microtime'] < $call->time[CDaemon_Worker_MediatorAbstract::UNCALLED])    // Has been requeued - Cancel this call
                        $call->cancelled();
                    return $call;
                };
                break;
            case CDaemon_Worker_MediatorAbstract::RETURNED:
                $decoder = function($message) use($that) {
                    $call = shm_get_var($that->shm, $message['call_id']);
                    if ($call && $call->status == $message['status']) {
                        @shm_remove_var($that->shm, $message['call_id']);
                    }
                    return $call;
                };
                break;
            default:
                $decoder = function($message) use($that) {
                    $call = $that->mediator->getStruct($message['call_id']);
                    // If we don't have a local copy of $call the most likely scenario is a --recoverworkers situation.
                    // Create a placeholder. We'll get a full copy of the struct when it's returned from the worker
                    if (!$call) {
                        $call = new CDaemon_Worker_Call($message['call_id']);
                    }
                    $call->status($message['status']);
                    $call->pid = $message['pid'];
                    return $call;
                };
        }
        // Now get on with decoding the $message
        $tries = 1;
        do {
            $call = $decoder($message);
        } while (empty($call) && $this->error(null, $tries) && $tries++ < 3);
        if (!is_object($call))
            throw new Exception(__METHOD__ . " Failed. Could Not Decode Message: " . print_r($message, true));
        if (!$this->memoryAllocationWarning && $call->size > ($this->memoryAllocation / 50)) {
            $this->memoryAllocationWarning = true;
            $suggested_size = $call->size * 60;
            $this->mediator->log("WARNING: The memory allocated to this worker is too low and may lead to out-of-shared-memory errors.\n" .
                    "         Based on this job, the memory allocation should be at least {$suggested_size} bytes. Current allocation: {$this->memoryAllocation} bytes.");
        }
        return $call;
    }

    /**
     * The state of the queue -- The number of pending messages, memory consumption, errors, etc.
     * @return Array with some subset of these keys: messages, memoryAllocation, error_count
     */
    public function state() {
        $out = array(
            'messages' => null,
            'memoryAllocation' => null,
        );
        $stat = @msg_stat_queue($this->queue);
        if (is_array($stat)) {
            $out['messages'] = $stat['msg_qnum'];
        }
        $header = @shm_get_var($this->shm, 1);
        if (is_array($header)) {
            $out['memoryAllocation'] = $header['memoryAllocation'];
        }
        return $out;
    }

    /**
     * Drop any pending messages in the queue
     * @return boolean
     */
    public function purge() {
        $this->purge_mq();
        $this->purge_shm();
        $this->setupIpc();
    }

    /**
     * The interface forces us to expose a purge() method -- but SysV separates the queue from the payloads
     * so implement these methods to give us finer control while error handling.
     * @return void
     */
    private function purge_shm() {
        if (!is_resource($this->shm))
            $this->setupIpc();
        @shm_remove($this->shm);
        @shm_detach($this->shm);
        $this->shm = null;
    }

    /**
     * The interface forces us to expose a purge() method -- but SysV separates the queue from the payloads
     * so implement these methods to give us finer control while error handling.
     * @return void
     */
    private function purge_mq() {
        if (!is_resource($this->queue)) {
            $this->setupIpc();
        }
        @msg_remove_queue($this->queue);
        $this->queue = null;
    }

    /**
     * Handle IPC Errors
     * @param $error
     * @param int $try    Inform error() of repeated failures of the same $error_code
     * @return boolean  Returns true if the operation should be retried.
     */
    public function error($error, $try = 1) {
        // Create an array of random, moderate size and verify it can be written to shared memory
        // Return boolean
        $that = $this;
        $test = function() use($that) {
            $arr = array_fill(0, mt_rand(10, 100), mt_rand(1000, 1000 * 1000));
            $key = mt_rand(1000 * 1000, 2000 * 1000);
            @shm_put_var($that->shm, $key, $arr);
            usleep(5000);
            return @shm_get_var($that->shm, $key) == $arr;
        };
        switch ($error) {
            case 0:             // Success
            case 4:             // System Interrupt
            case MSG_ENOMSG:    // No message of desired type
                // Ignored Errors
                return true;
                break;
            case MSG_EAGAIN:    // Temporary Problem, Try Again
                usleep($this->mediator->backoff(20000, $try));
                return true;
                break;
            case 13:
                // Permission Denied
                $this->mediator->countError('communication');
                $this->mediator->log('Permission Denied: Cannot connect to message queue');
                $this->purge_mq();
                if ($this->mediator->service->isParent())
                    usleep($this->mediator->backoff(100000, $try));
                else
                    sleep($this->mediator->backoff(3, $try));
                $this->setupIpc();
                return true;
                break;
            case 22:
            // Invalid Argument
            // Probably because the queue was removed in another process.
            case 43:
                // Identifier Removed
                // A message queue was re-created at this address but the resource identifier we have needs to be re-created
                $this->mediator->count_error('communication');
                if ($this->mediator->service->isParent()) {
                    usleep($this->mediator->backoff(20000, $try));
                } else {
                    sleep($this->mediator->backoff(2, $try));
                }
                $this->setupIpc();
                return true;
                break;
            case self::ERROR_UNKNOWN:
                // Almost certainly an issue with shared memory
                $this->mediator->log("Shared Memory I/O Error at Address {$this->mediator->guid}.");
                $this->mediator->count_error('corruption');
                // If this is a worker, all we can do is try to re-attach the shared memory.
                // Any corruption or OOM errors will be handled by the parent exclusively.
                if (!$this->mediator->service->isParent()) {
                    sleep($this->mediator->backoff(3, $try));
                    $this->setupIpc();
                    return true;
                }
                // If this is the parent, do some diagnostic checks and attempt correction.
                usleep($this->mediator->backoff(20000, $try));
                // Test writing to shared memory using an array that should come to a few kilobytes.
                for ($i = 0; $i < 2; $i++) {
                    if ($test())
                        return true;
                    // Re-attach the shared memory and try the diagnostic again
                    $this->setupIpc();
                }
                $this->mediator->log("IPC DIAG: Re-Connect failed to solve the problem.");
                if (!$this->mediator->service->isParent()) {
                    break;
                }
                // Attempt to re-connect the shared memory
                // See if we can read what's in shared memory and re-write it later
                $items_to_copy = array();
                $itemsToCall = array();
                for ($i = 0; $i < $this->mediator->callCount; $i++) {
                    $call = @shm_get_var($this->shm, $i);
                    if (!is_object($call)) {
                        continue;
                    }
                    $cached = $this->mediator->getStruct($i);
                    if (!is_object($cached)) {
                        continue;
                    }
                    if ($cached->status == CDaemon_Worker_MediatorAbstract::TIMEOUT) {
                        continue;
                    }
                    if ($cached->status == CDaemon_Worker_MediatorAbstract::UNCALLED) {
                        $itemsToCall[$i] = $call;
                        continue;
                    }
                    $items_to_copy[$i] = $call;
                }
                $this->mediator->log("IPC DIAG: Preparing to clean SHM and Reconnect...");
                for ($i = 0; $i < 2; $i++) {
                    $this->purge_shm();
                    $this->setupIpc();
                    if (!empty($items_to_copy))
                        foreach ($items_to_copy as $key => $value)
                            @shm_put_var($this->shm, $key, $value);
                    if (!$test()) {
                        if (empty($items_to_copy)) {
                            $this->mediator->fatal_error("Shared Memory Failure: Unable to proceed.");
                        } else {
                            $this->mediator->log('IPC DIAG: Purging items from shared memory: ' . implode(', ', array_keys($items_to_copy)));
                            unset($items_to_copy);
                        }
                    }
                }
                foreach ($itemsToCall as $call) {
                    $this->mediator->retry($call);
                }
                return true;
            default:
                if ($error)
                    $this->mediator->log("Message Queue Error {$error}: " . posix_strerror($error));
                if ($this->mediator->service->isParent()) {
                    usleep($this->mediator->backoff(100000, $try));
                } else {
                    sleep($this->mediator->backoff(3, $try));
                }
                $this->mediator->countError('catchall');
                $this->setupIpc();
                return false;
        }
    }

    /**
     * Drop the single message
     * @return void
     */
    public function drop($call_id) {
        if (shm_has_var($this->shm, $call_id)) {
            shm_remove_var($this->shm, $call_id);
        }
    }

    /**
     * Remove and release shared memory and message queue resources
     * @return mixed
     */
    public function release() {
        if ($this->shm) {
            @shm_remove($this->shm);
        }
        if ($this->queue) {
            @msg_remove_queue($this->queue);
        }
    }

}
