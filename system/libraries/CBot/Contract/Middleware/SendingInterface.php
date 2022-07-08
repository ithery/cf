<?php

interface CBot_Contract_Middleware_SendingInterface {
    /**
     * Handle an outgoing message payload before/after it
     * hits the message service.
     *
     * @param mixed    $payload
     * @param callable $next
     * @param CBot_Bot $bot
     *
     * @return mixed
     */
    public function sending($payload, $next, CBot_Bot $bot);
}
