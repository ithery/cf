<?php
class CBot_Message_Matching_MatchingMessage {
    /**
     * @var CBot_Command
     */
    protected $command;

    /**
     * @var CBot_Message_Incoming_IncomingMessage
     */
    protected $message;

    /**
     * @var array
     */
    private $matches;

    /**
     * MatchingMessage constructor.
     *
     * @param CBot_Command                          $command
     * @param CBot_Message_Incoming_IncomingMessage $message
     * @param array                                 $matches
     */
    public function __construct(CBot_Command $command, CBot_Message_Incoming_IncomingMessage $message, array $matches) {
        $this->command = $command;
        $this->message = $message;
        $this->matches = $matches;
    }

    /**
     * @return CBot_Command
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * @return \CBot_Message_Incoming_IncomingMessage
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getMatches() {
        return $this->matches;
    }
}
