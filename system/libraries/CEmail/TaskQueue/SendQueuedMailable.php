<?php
class CEmail_TaskQueue_SendQueuedMailable {
    use CQueue_Trait_QueueableTrait;

    /**
     * The mailable message instance.
     *
     * @var \CEmail_Contract_MailableInterface
     */
    public $mailable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout;

    /**
     * Indicates if the job should be encrypted.
     *
     * @var bool
     */
    public $shouldBeEncrypted = false;

    /**
     * Create a new job instance.
     *
     * @param \CEmail_Contract_MailableInterface $mailable
     *
     * @return void
     */
    public function __construct(CEmail_Contract_MailableInterface $mailable) {
        $this->mailable = $mailable;
        $this->tries = property_exists($mailable, 'tries') ? $mailable->tries : null;
        $this->timeout = property_exists($mailable, 'timeout') ? $mailable->timeout : null;
        $this->afterCommit = property_exists($mailable, 'afterCommit') ? $mailable->afterCommit : null;
        $this->shouldBeEncrypted = $mailable instanceof CQueue_Contract_ShouldBeEncryptedInterface;
    }

    /**
     * Handle the queued job.
     *
     * @param \CEmail_Contract_FactoryInterface $factory
     *
     * @return void
     */
    public function handle(CEmail_Contract_FactoryInterface $factory) {
        $this->mailable->send($factory);
    }

    /**
     * Get the number of seconds before a released mailable will be available.
     *
     * @return mixed
     */
    public function backoff() {
        if (!method_exists($this->mailable, 'backoff') && !isset($this->mailable->backoff)) {
            return;
        }

        return $this->mailable->backoff ?? $this->mailable->backoff();
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return null|\DateTime
     */
    public function retryUntil() {
        if (!method_exists($this->mailable, 'retryUntil') && !isset($this->mailable->retryUntil)) {
            return;
        }

        return $this->mailable->retryUntil ?? $this->mailable->retryUntil();
    }

    /**
     * Call the failed method on the mailable instance.
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function failed($e) {
        if (method_exists($this->mailable, 'failed')) {
            $this->mailable->failed($e);
        }
    }

    /**
     * Get the display name for the queued job.
     *
     * @return string
     */
    public function displayName() {
        return get_class($this->mailable);
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone() {
        $this->mailable = clone $this->mailable;
    }
}
