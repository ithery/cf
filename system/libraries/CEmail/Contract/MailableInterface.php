<?php

interface CEmail_Contract_MailableInterface {
    /**
     * Send the message using the given mailer.
     *
     * @param \CEmail_Contract_FactoryInterface|\CEmail_Contract_MailerInterface $mailer
     *
     * @return null|\CEmail_SentMessage
     */
    public function send($mailer);

    /**
     * Queue the given message.
     *
     * @param \CQueue_FactoryInterface $queue
     *
     * @return mixed
     */
    public function queue(CQueue_FactoryInterface $queue);

    /**
     * Deliver the queued message after (n) seconds.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param \Illuminate\Contracts\Queue\Factory  $queue
     *
     * @return mixed
     */
    public function later($delay, CQueue_FactoryInterface $queue);

    /**
     * Set the recipients of the message.
     *
     * @param object|array|string $address
     * @param null|string         $name
     *
     * @return self
     */
    public function cc($address, $name = null);

    /**
     * Set the recipients of the message.
     *
     * @param object|array|string $address
     * @param null|string         $name
     *
     * @return $this
     */
    public function bcc($address, $name = null);

    /**
     * Set the recipients of the message.
     *
     * @param object|array|string $address
     * @param null|string         $name
     *
     * @return $this
     */
    public function to($address, $name = null);

    /**
     * Set the locale of the message.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function locale($locale);

    /**
     * Set the name of the mailer that should be used to send the message.
     *
     * @param string $mailer
     *
     * @return $this
     */
    public function mailer($mailer);
}
