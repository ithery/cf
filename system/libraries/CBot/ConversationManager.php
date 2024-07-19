<?php

class CBot_ConversationManager {
    protected $matcher;

    /**
     * Messages to listen to.
     *
     * @var CBot_Command[]
     */
    protected $listenTo = [];

    public function __construct(CBot_Message_Matcher $matcher = null) {
        $this->matcher = $matcher ?: new CBot_Message_Matcher();
    }

    public function listenTo(CBot_Command $command) {
        $this->listenTo[] = $command;
    }

    /**
     * Add additional data (image,video,audio,location,files) data to
     * callable parameters.
     *
     * @param CBot_Message_Incoming_IncomingMessage $message
     * @param array                                 $parameters
     *
     * @return array
     */
    public function addDataParameters(CBot_Message_Incoming_IncomingMessage $message, array $parameters) {
        $messageText = $message->getText();

        if ($messageText === CBot_Message_Attachment_Image::PATTERN) {
            $parameters[] = $message->getImages();
        } elseif ($messageText === CBot_Message_Attachment_Video::PATTERN) {
            $parameters[] = $message->getVideos();
        } elseif ($messageText === CBot_Message_Attachment_Audio::PATTERN) {
            $parameters[] = $message->getAudio();
        } elseif ($messageText === CBot_Message_Attachment_Location::PATTERN) {
            $parameters[] = $message->getLocation();
        } elseif ($messageText === CBot_Message_Attachment_Contact::PATTERN) {
            $parameters[] = $message->getContact();
        } elseif ($messageText === CBot_Message_Attachment_File::PATTERN) {
            $parameters[] = $message->getFiles();
        }

        return $parameters;
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage[] $messages
     * @param CBot_MiddlewareManager                  $middleware
     * @param CBot_Message_Incoming_Answer            $answer
     * @param CBot_Contract_DriverInterface           $driver
     * @param bool                                    $withReceivedMiddleware
     *
     * @return array|CBot_Message_Matching_MatchingMessage[]
     */
    public function getMatchingMessages(
        $messages,
        CBot_MiddlewareManager $middleware,
        CBot_Message_Incoming_Answer $answer,
        CBot_Contract_DriverInterface $driver,
        $withReceivedMiddleware = true
    ) {
        $messages = c::collect($messages)->reject(function (CBot_Message_Incoming_IncomingMessage $message) {
            return $message->isFromBot();
        });

        $matchingMessages = [];
        foreach ($messages as $message) {
            if ($withReceivedMiddleware) {
                $message = $middleware->applyMiddleware('received', $message);
            }

            foreach ($this->listenTo as $command) {
                if ($this->matcher->isMessageMatching($message, $answer, $command, $driver, $middleware->matching())) {
                    $matchingMessages[] = new CBot_Message_Matching_MatchingMessage($command, $message, $this->matcher->getMatches());
                }
            }
        }

        return $matchingMessages;
    }
}
