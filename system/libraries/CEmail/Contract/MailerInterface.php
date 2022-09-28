<?php

interface CEmail_Contract_MailerInterface {
    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param mixed $users
     *
     * @return \CEmail_PendingMail
     */
    public function to($users);

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param mixed $users
     *
     * @return \CEmail_PendingMail
     */
    public function bcc($users);

    /**
     * Send a new message with only a raw text part.
     *
     * @param string $text
     * @param mixed  $callback
     *
     * @return null|\Illuminate\Mail\SentMessage
     */
    public function raw($text, $callback);

    /**
     * Send a new message using a view.
     *
     * @param \Illuminate\Contracts\Mail\Mailable|string|array $view
     * @param array                                            $data
     * @param null|\Closure|string                             $callback
     *
     * @return null|\Illuminate\Mail\SentMessage
     */
    public function send($view, array $data = [], $callback = null);
}
