<?php

class CBot_Message_Matcher {
    /**
     * Regular expression to capture named parameters but not quantifiers
     * captures {name}, but not {1}, {1,}, or {1,2}.
     */
    const PARAM_NAME_REGEX = '/\{((?:(?!\d+,?\d+?)\w)+?)\}/';

    /**
     * @var array
     */
    protected $matches;

    /**
     * @param CBot_Message_Incoming_IncomingMessage        $message
     * @param CBot_Message_Incoming_Answer                 $answer
     * @param CBot_Command                                 $command
     * @param CBot_Contract_DriverInterface                $driver
     * @param CBot_Contract_Middleware_MatchingInterface[] $middleware
     *
     * @return bool
     */
    public function isMessageMatching(
        CBot_Message_Incoming_IncomingMessage $message,
        CBot_Message_Incoming_Answer $answer,
        CBot_Command $command,
        CBot_Contract_DriverInterface $driver,
        $middleware = []
    ) {
        return $this->isDriverValid($driver->getName(), $command->getDriver())
            && $this->isRecipientValid($message->getRecipient(), $command->getRecipients())
            && $this->isPatternValid($message, $answer, $command->getPattern(), $command->getMiddleware() + $middleware);
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage        $message
     * @param CBot_Message_Incoming_Answer                 $answer
     * @param string                                       $pattern
     * @param CBot_Contract_Middleware_MatchingInterface[] $middleware
     *
     * @return int
     */
    public function isPatternValid(
        CBot_Message_Incoming_IncomingMessage $message,
        CBot_Message_Incoming_Answer $answer,
        $pattern,
        $middleware = []
    ) {
        $this->matches = [];

        $answerText = $answer->getValue();
        if (is_array($answerText)) {
            $answerText = '';
        }

        $pattern = str_replace('/', '\/', $pattern);
        $text = '/^' . preg_replace(self::PARAM_NAME_REGEX, '(?<$1>.*)', $pattern) . ' ?$/miu';

        $regexMatched = (bool) preg_match($text, (string) $message->getText(), $this->matches) || (bool) preg_match($text, (string) $answerText, $this->matches);

        // Try middleware first
        if (count($middleware)) {
            return c::collect($middleware)->reject(function (CBot_Contract_Middleware_MatchingInterface $middleware) use (
                $message,
                $pattern,
                $regexMatched
            ) {
                return $middleware->matching($message, $pattern, $regexMatched);
            })->isEmpty() === true;
        }

        return $regexMatched;
    }

    /**
     * @param string       $driverName
     * @param string|array $allowedDrivers
     *
     * @return bool
     */
    protected function isDriverValid($driverName, $allowedDrivers) {
        if (!is_null($allowedDrivers)) {
            return c::collect($allowedDrivers)->contains($driverName);
        }

        return true;
    }

    /**
     * @param $givenRecipient
     * @param $allowedRecipients
     *
     * @return bool
     */
    protected function isRecipientValid($givenRecipient, $allowedRecipients) {
        if (null === $allowedRecipients) {
            return true;
        }

        return in_array($givenRecipient, $allowedRecipients);
    }

    /**
     * @return array
     */
    public function getMatches() {
        return array_slice($this->matches, 1);
    }
}
