<?php

interface CBot_Contract_Middleware_ReceivedInterface {
    /**
     * Handle an incoming message.
     *
     * @param CBot_Message_Incoming_IncomingMessage $message
     * @param callable                              $next
     * @param CBot_Bot                              $bot
     *
     * @return mixed
     */
    public function received(CBot_Message_Incoming_IncomingMessage $message, $next, CBot_Bot $bot);
}
