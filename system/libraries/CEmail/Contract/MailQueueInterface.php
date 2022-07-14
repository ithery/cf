<?php
interface CEmail_Contract_MailQueueInterface {
    /**
     * Queue a new e-mail message for sending.
     *
     * @param \CEmail_Contract_MailableInterface|string|array $view
     * @param null|string                                     $queue
     *
     * @return mixed
     */
    public function queue($view, $queue = null);

    /**
     * Queue a new e-mail message for sending after (n) seconds.
     *
     * @param \DateTimeInterface|\DateInterval|int            $delay
     * @param \CEmail_Contract_MailableInterface|string|array $view
     * @param null|string                                     $queue
     *
     * @return mixed
     */
    public function later($delay, $view, $queue = null);
}
