<?php

interface CBot_Contract_Middleware_MatchingInterface {
    /**
     * @param CBot_Message_Incoming_IncomingMessage $message
     * @param string                                $pattern
     * @param bool                                  $regexMatched Indicator if the regular expression was matched too
     *
     * @return bool
     */
    public function matching(CBot_Message_Incoming_IncomingMessage $message, $pattern, $regexMatched);
}
