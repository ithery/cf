<?php
interface CBot_Contract_Middleware_CapturedInterface {
    /**
     * Handle a captured message.
     *
     * @param CBot_Message_Incoming_IncomingMessage $message
     * @param callable                              $next
     * @param CBot_Bot                              $bot
     *
     * @return mixed
     */
    public function captured(CBot_Message_Incoming_IncomingMessage $message, $next, CBot_Bot $bot);
}
