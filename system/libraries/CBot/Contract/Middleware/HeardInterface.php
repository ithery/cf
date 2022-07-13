<?php

interface CBot_Contract_Middleware_HeardInterface {
    /**
     * Handle a message that was successfully heard, but not processed yet.
     *
     * @param CBot_Message_Incoming_IncomingMessage $message
     * @param callable                              $next
     * @param CBot_Bot                              $bot
     *
     * @return mixed
     */
    public function heard(CBot_Message_Incoming_IncomingMessage $message, $next, CBot_Bot $bot);
}
