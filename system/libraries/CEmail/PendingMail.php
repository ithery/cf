<?php

use CEmail_Contract_MailerInterface as MailerContract;
use CEmail_Contract_MailableInterface as MailableContract;

class CEmail_PendingMail {
    use CTrait_Conditionable;

    /**
     * The mailer instance.
     *
     * @var \CEmail_Contract_MailerInterface
     */
    protected $mailer;

    /**
     * The locale of the message.
     *
     * @var string
     */
    protected $locale;

    /**
     * The "to" recipients of the message.
     *
     * @var array
     */
    protected $to = [];

    /**
     * The "cc" recipients of the message.
     *
     * @var array
     */
    protected $cc = [];

    /**
     * The "bcc" recipients of the message.
     *
     * @var array
     */
    protected $bcc = [];

    /**
     * Create a new mailable mailer instance.
     *
     * @param \CEmail_Contract_MailerInterface $mailer
     *
     * @return void
     */
    public function __construct(CEmail_Contract_MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    /**
     * Set the locale of the message.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function locale($locale) {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Set the recipients of the message.
     *
     * @param mixed $users
     *
     * @return $this
     */
    public function to($users) {
        $this->to = $users;

        if (!$this->locale && $users instanceof CTranslation_Contract_HasLocalePreferenceInterface) {
            $this->locale($users->preferredLocale());
        }

        return $this;
    }

    /**
     * Set the recipients of the message.
     *
     * @param mixed $users
     *
     * @return $this
     */
    public function cc($users) {
        $this->cc = $users;

        return $this;
    }

    /**
     * Set the recipients of the message.
     *
     * @param mixed $users
     *
     * @return $this
     */
    public function bcc($users) {
        $this->bcc = $users;

        return $this;
    }

    /**
     * Send a new mailable message instance.
     *
     * @param \CEmail_Contract_MailableInterface $mailable
     *
     * @return null|\CEmail_SentMessage
     */
    public function send(MailableContract $mailable) {
        return $this->mailer->send($this->fill($mailable));
    }

    /**
     * Push the given mailable onto the queue.
     *
     * @param \CEmail_Contract_MailableInterface $mailable
     *
     * @return mixed
     */
    public function queue(MailableContract $mailable) {
        return $this->mailer->queue($this->fill($mailable));
    }

    /**
     * Deliver the queued message after (n) seconds.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param \CEmail_Contract_MailableInterface   $mailable
     *
     * @return mixed
     */
    public function later($delay, MailableContract $mailable) {
        return $this->mailer->later($delay, $this->fill($mailable));
    }

    /**
     * Populate the mailable with the addresses.
     *
     * @param \CEmail_Contract_MailableInterface $mailable
     *
     * @return \CEmail_Mailable
     */
    protected function fill(MailableContract $mailable) {
        return c::tap($mailable->to($this->to)
            ->cc($this->cc)
            ->bcc($this->bcc), function (MailableContract $mailable) {
                if ($this->locale) {
                    $mailable->locale($this->locale);
                }
            });
    }
}
