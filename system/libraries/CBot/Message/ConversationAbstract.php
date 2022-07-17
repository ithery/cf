<?php

use BotMan\BotMan\BotMan;
use CBot_Message_Outgoing_Question;
use BotMan\BotMan\Interfaces\ShouldQueue;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Attachments\Contact;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

/**
 * Class Conversation.
 */
abstract class CBot_Message_ConversationAbstract {
    use CTrait_Macroable;

    /**
     * @var CBot_Bot
     */
    protected $bot;

    /**
     * @var string
     */
    protected $token;

    /**
     * Number of minutes this specific conversation should be cached.
     *
     * @var int
     */
    protected $cacheTime;

    /**
     * @param CBot_Bot $bot
     */
    public function setBot(CBot_Bot $bot) {
        $this->bot = $bot;
    }

    /**
     * @return CBot_Bot
     */
    public function getBot() {
        return $this->bot;
    }

    /**
     * @param string|Question $question
     * @param array|Closure   $next
     * @param array           $additionalParameters
     *
     * @return $this
     */
    public function ask($question, $next, $additionalParameters = []) {
        $this->bot->reply($question, $additionalParameters);
        $this->bot->storeConversation($this, $next, $question, $additionalParameters);

        return $this;
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $question
     * @param array|Closure                          $next
     * @param array|Closure                          $repeat
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function askForImages($question, $next, $repeat = null, $additionalParameters = []) {
        $additionalParameters['__getter'] = 'getImages';
        $additionalParameters['__pattern'] = CBot_Message_Attachment_Image::PATTERN;
        $additionalParameters['__repeat'] = !is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $question
     * @param array|Closure                          $next
     * @param array|Closure                          $repeat
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function askForFiles($question, $next, $repeat = null, $additionalParameters = []) {
        $additionalParameters['__getter'] = 'getFiles';
        $additionalParameters['__pattern'] = CBot_Message_Attachment_File::PATTERN;
        $additionalParameters['__repeat'] = !is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $question
     * @param array|Closure                          $next
     * @param array|Closure                          $repeat
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function askForVideos($question, $next, $repeat = null, $additionalParameters = []) {
        $additionalParameters['__getter'] = 'getVideos';
        $additionalParameters['__pattern'] = CBot_Message_Attachment_Video::PATTERN;
        $additionalParameters['__repeat'] = !is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $question
     * @param array|Closure                          $next
     * @param array|Closure                          $repeat
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function askForAudio($question, $next, $repeat = null, $additionalParameters = []) {
        $additionalParameters['__getter'] = 'getAudio';
        $additionalParameters['__pattern'] = CBot_Message_Attachment_Audio::PATTERN;
        $additionalParameters['__repeat'] = !is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $question
     * @param array|Closure                          $next
     * @param array|Closure                          $repeat
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function askForLocation($question, $next, $repeat = null, $additionalParameters = []) {
        $additionalParameters['__getter'] = 'getLocation';
        $additionalParameters['__pattern'] = CBot_Message_Attachment_Location::PATTERN;
        $additionalParameters['__repeat'] = !is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $question
     * @param array|Closure                          $next
     * @param array|Closure                          $repeat
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function askForContact($question, $next, $repeat = null, $additionalParameters = []) {
        $additionalParameters['__getter'] = 'getContact';
        $additionalParameters['__pattern'] = CBot_Message_Attachment_Contact::PATTERN;
        $additionalParameters['__repeat'] = !is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * Repeat the previously asked question.
     *
     * @param string|CBot_Message_Outgoing_Question $question
     */
    public function repeat($question = '') {
        $conversation = $this->bot->getStoredConversation();

        if (!$question instanceof CBot_Message_Outgoing_Question && !$question) {
            $question = unserialize($conversation['question']);
        }

        $next = $conversation['next'];
        $additionalParameters = unserialize($conversation['additionalParameters']);

        if (is_string($next)) {
            $next = unserialize($next)->getClosure();
        } elseif (is_array($next)) {
            $next = c::collect($next)->map(function ($callback) {
                if ($this->bot->getDriver()->serializesCallbacks() && !$this->bot->runsOnSocket()) {
                    $callback['callback'] = unserialize($callback['callback'])->getClosure();
                }

                return $callback;
            })->toArray();
        }
        $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $message
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function say($message, $additionalParameters = []) {
        $this->bot->reply($message, $additionalParameters);

        return $this;
    }

    /**
     * Should the conversation be skipped (temporarily).
     *
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return bool
     */
    public function skipsConversation(CBot_Message_Incoming_IncomingMessage $message) {
    }

    /**
     * Should the conversation be removed and stopped (permanently).
     *
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return bool
     */
    public function stopsConversation(CBot_Message_Incoming_IncomingMessage $message) {
    }

    /**
     * Override default conversation cache time (only for this conversation).
     *
     * @return mixed
     */
    public function getConversationCacheTime() {
        return $this->cacheTime ?? null;
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * @return array
     */
    public function __sleep() {
        $properties = get_object_vars($this);
        if (!$this instanceof CQueue_ShouldQueueInterface) {
            unset($properties['bot']);
        }

        return array_keys($properties);
    }
}
