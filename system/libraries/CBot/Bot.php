<?php
/**
 * @see CBot
 */
class CBot_Bot {
    use CBot_Trait_ProvidesStorageTrait;
    use CBot_Trait_HandlesConversationsTrait;
    use CBot_Trait_HandlesExceptionsTrait;

    /**
     * @var CBot_MiddlewareManager
     */
    public $middleware;

    /**
     * @var \CCollection
     */
    protected $event;

    /**
     * @var CBot_Command
     */
    protected $command;

    /**
     * @var CBot_Message_Incoming_IncomingMessage
     */
    protected $message;

    /**
     * @var CBot_Message_Outgoing_OutgoingMessage|CBot_Message_Outgoing_Question
     */
    protected $outgoingMessage;

    /**
     * @var string
     */
    protected $driverName;

    /**
     * @var null|array
     */
    protected $currentConversationData;

    /**
     * @var CBot_Contract_ExceptionHandlerInterface
     */
    protected $exceptionHandler;

    /**
     * IncomingMessage service events.
     *
     * @var array
     */
    protected $events = [];

    /**
     * The fallback message to use, if no match
     * could be heard.
     *
     * @var null|callable
     */
    protected $fallbackMessage;

    /**
     * @var array
     */
    protected $groupAttributes = [];

    /**
     * @var array
     */
    protected $config;

    /**
     * @var CBot_Contract_DriverInterface
     */
    protected $driver;

    /**
     * @var CBot_ConversationManager
     */
    protected $conversationManager;

    /**
     * @var CBot_Message_Matcher
     */
    protected $matcher;

    /**
     * @var CBot_Contract_StorageInterface
     */
    protected $storage;

    /**
     * @var bool
     */
    protected $loadedConversation = false;

    /**
     * @var bool
     */
    protected $firedDriverEvents = false;

    /**
     * @var bool
     */
    protected $runsOnSocket = false;

    protected $matches;

    /**
     * @var CCache_Repository
     */
    private $cache;

    public function __construct(
        array $config,
        CBot_Contract_DriverInterface $driver,
        CBot_Contract_StorageInterface $storage,
        CCache_Repository $cache,
        CBot_Message_Matcher $matcher = null
    ) {
        $this->config = $config;
        $this->driver = $driver;
        $this->cache = $cache;
        $this->storage = $storage;
        $this->message = new CBot_Message_Incoming_IncomingMessage('', '', '', null, $this->config['bot_id']);
        $this->matcher = new CBot_Message_Matcher();
        $this->middleware = new CBot_MiddlewareManager($this);
        $this->conversationManager = new CBot_ConversationManager($matcher);
        $this->exceptionHandler = new CBot_ExceptionHandler();
    }

    /**
     * Set a fallback message to use if no listener matches.
     *
     * @param callable $callback
     */
    public function fallback($callback) {
        $this->fallbackMessage = $callback;
    }

    /**
     * @param string $name The Driver name or class
     */
    public function loadDriver($name) {
        $this->driver = CBot_DriverManager::loadFromName($name, $this->config);
    }

    /**
     * @param CBot_Contract_DriverInterface $driver
     */
    public function setDriver(CBot_Contract_DriverInterface $driver) {
        $this->driver = $driver;
    }

    /**
     * @return CBot_Contract_DriverInterface
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages() {
        return $this->getDriver()->getMessages();
    }

    /**
     * Retrieve the chat message that are sent from bots.
     *
     * @return array
     */
    public function getBotMessages() {
        return c::collect($this->getDriver()->getMessages())->filter(function (CBot_Message_Incoming_IncomingMessage $message) {
            return $message->isFromBot();
        })->toArray();
    }

    /**
     * @return CBot_Message_Incoming_Answer
     */
    public function getConversationAnswer() {
        return $this->getDriver()->getConversationAnswer($this->message);
    }

    /**
     * @param bool $running
     *
     * @return bool
     */
    public function runsOnSocket($running = null) {
        if (\is_bool($running)) {
            $this->runsOnSocket = $running;
        }

        return $this->runsOnSocket;
    }

    /**
     * @return CBot_Contract_UserInterface
     */
    public function getUser() {
        if ($user = $this->cache->get('user_' . $this->driver->getName() . '_' . $this->getMessage()->getSender())) {
            return $user;
        }

        $user = $this->getDriver()->getUser($this->getMessage());
        $this->cache->put(
            'user_' . $this->driver->getName() . '_' . $user->getId(),
            $user,
            $this->config['user_cache_time'] ?? 30 * 60
        );

        return $user;
    }

    /**
     * Get the parameter names for the route.
     *
     * @param $value
     *
     * @return array
     */
    protected function compileParameterNames($value) {
        preg_match_all(CBot_Message_Matcher::PARAM_NAME_REGEX, $value, $matches);

        return array_map(function ($m) {
            return trim($m, '?');
        }, $matches[1]);
    }

    /**
     * @param array|string   $pattern  the pattern to listen for
     * @param Closure|string $callback the callback to execute. Either a closure or a Class@method notation
     * @param string         $in       the channel type to listen to (either direct message or public channel)
     *
     * @return CBot_Command
     */
    public function hears($pattern, $callback, $in = null) {
        if (is_array($pattern)) {
            $pattern = '(?|' . implode('|', $pattern) . ')';
        }

        $command = new CBot_Command($pattern, $callback, $in);
        $command->applyGroupAttributes($this->groupAttributes);

        $this->conversationManager->listenTo($command);

        return $command;
    }

    /**
     * Listen for messaging service events.
     *
     * @param array|string   $names
     * @param Closure|string $callback
     */
    public function on($names, $callback) {
        if (!is_array($names)) {
            $names = [$names];
        }

        $callable = $this->getCallable($callback);

        foreach ($names as $name) {
            $this->events[] = [
                'name' => $name,
                'callback' => $callable,
            ];
        }
    }

    /**
     * Listening for image files.
     *
     * @param $callback
     *
     * @return CBot_Command
     */
    public function receivesImages($callback) {
        return $this->hears(CBot_Message_Attachment_Image::PATTERN, $callback);
    }

    /**
     * Listening for video files.
     *
     * @param $callback
     *
     * @return CBot_Command
     */
    public function receivesVideos($callback) {
        return $this->hears(CBot_Message_Attachment_Video::PATTERN, $callback);
    }

    /**
     * Listening for audio files.
     *
     * @param $callback
     *
     * @return CBot_Command
     */
    public function receivesAudio($callback) {
        return $this->hears(CBot_Message_Attachment_Audio::PATTERN, $callback);
    }

    /**
     * Listening for location attachment.
     *
     * @param $callback
     *
     * @return CBot_Command
     */
    public function receivesLocation($callback) {
        return $this->hears(CBot_Message_Attachment_Location::PATTERN, $callback);
    }

    /**
     * Listening for contact attachment.
     *
     * @param $callback
     *
     * @return CBot_Command
     */
    public function receivesContact($callback) {
        return $this->hears(CBot_Message_Attachment_Contact::PATTERN, $callback);
    }

    /**
     * Listening for files attachment.
     *
     * @param $callback
     *
     * @return CBot_Command
     */
    public function receivesFiles($callback) {
        return $this->hears(CBot_Message_Attachment_File::PATTERN, $callback);
    }

    /**
     * Create a command group with shared attributes.
     *
     * @param array    $attributes
     * @param \Closure $callback
     */
    public function group(array $attributes, Closure $callback) {
        $previousGroupAttributes = $this->groupAttributes;
        $this->groupAttributes = array_merge_recursive($previousGroupAttributes, $attributes);

        \call_user_func($callback, $this);

        $this->groupAttributes = $previousGroupAttributes;
    }

    /**
     * Fire potential driver event callbacks.
     */
    protected function fireDriverEvents() {
        $driverEvent = $this->getDriver()->hasMatchingEvent();
        if ($driverEvent instanceof CBot_Contract_DriverEventInterface) {
            $this->firedDriverEvents = true;

            c::collect($this->events)->filter(function ($event) use ($driverEvent) {
                return $driverEvent->getName() === $event['name'];
            })->each(function ($event) use ($driverEvent) {
                /**
                 * Load the message, so driver events can reply.
                 */
                $messages = $this->getDriver()->getMessages();
                if (isset($messages[0])) {
                    $this->message = $messages[0];
                }

                \call_user_func_array($event['callback'], [$driverEvent->getPayload(), $this]);
            });
        }
    }

    /**
     * Try to match messages with the ones we should
     * listen to.
     */
    public function listen() {
        try {
            $isVerificationRequest = $this->verifyServices();

            if (!$isVerificationRequest) {
                $this->fireDriverEvents();

                if ($this->firedDriverEvents === false) {
                    $this->loadActiveConversation();

                    if ($this->loadedConversation === false) {
                        $this->callMatchingMessages();
                    }
                }

                /**
                 * If the driver has a  "messagesHandled" method, call it.
                 * This method can be used to trigger driver methods
                 * once the messages are handles.
                 */
                if (method_exists($this->getDriver(), 'messagesHandled')) {
                    $this->getDriver()->messagesHandled();
                }

                $this->firedDriverEvents = false;
                $this->message = new CBot_Message_Incoming_IncomingMessage('', '', '', null, $this->config['bot_id']);
            }
        } catch (\Exception $e) {
            $this->exceptionHandler->handleException($e, $this);
        } catch (\Throwable $e) {
            $this->exceptionHandler->handleException($e, $this);
        }
    }

    /**
     * Call matching message callbacks.
     */
    protected function callMatchingMessages() {
        $matchingMessages = $this->conversationManager->getMatchingMessages(
            $this->getMessages(),
            $this->middleware,
            $this->getConversationAnswer(),
            $this->getDriver()
        );

        foreach ($matchingMessages as $matchingMessage) {
            $this->command = $matchingMessage->getCommand();
            $callback = $this->command->getCallback();

            $callback = $this->getCallable($callback);

            // Set the message first, so it's available for middlewares
            $this->message = $matchingMessage->getMessage();

            $commandMiddleware = c::collect($this->command->getMiddleware())->filter(function ($middleware) {
                return $middleware instanceof CBot_Contract_Middleware_HeardInterface;
            })->toArray();

            $this->message = $this->middleware->applyMiddleware(
                'heard',
                $matchingMessage->getMessage(),
                $commandMiddleware
            );

            $parameterNames = $this->compileParameterNames($this->command->getPattern());

            $parameters = $matchingMessage->getMatches();
            if (\count($parameterNames) !== \count($parameters)) {
                $parameters = array_merge(
                    //First, all named parameters (eg. function ($a, $b, $c))
                    array_filter(
                        $parameters,
                        '\is_string',
                        ARRAY_FILTER_USE_KEY
                    ),
                    //Then, all other unsorted parameters (regex non named results)
                    array_filter(
                        $parameters,
                        '\is_integer',
                        ARRAY_FILTER_USE_KEY
                    )
                );
            }

            $this->matches = $parameters;
            array_unshift($parameters, $this);

            $parameters = $this->conversationManager->addDataParameters($this->message, $parameters);

            if (call_user_func_array($callback, array_values($parameters))) {
                return;
            }
        }

        if (empty($matchingMessages) && empty($this->getBotMessages()) && !\is_null($this->fallbackMessage)) {
            $this->callFallbackMessage();
        }
    }

    /**
     * Call the fallback method.
     */
    protected function callFallbackMessage() {
        $messages = $this->getMessages();

        if (!isset($messages[0])) {
            return;
        }

        $this->message = $messages[0];

        $this->fallbackMessage = $this->getCallable($this->fallbackMessage);

        \call_user_func($this->fallbackMessage, $this);
    }

    /**
     * Verify service webhook URLs.
     *
     * @return bool
     */
    protected function verifyServices() {
        return CBot_DriverManager::verifyServices($this->config);
    }

    /**
     * @param string|CBot_Message_Outgoing_Question|CBot_Message_Outgoing_OutgoingMessage $message
     * @param string|array                                                                $recipients
     * @param null|string|DriverInterface                                                 $driver
     * @param array                                                                       $additionalParameters
     *
     * @throws CBot_Exception
     *
     * @return Response
     */
    public function say($message, $recipients, $driver = null, $additionalParameters = []) {
        if ($driver === null && $this->driver === null) {
            throw new CBot_Exception('The current driver can\'t be NULL');
        }

        $previousDriver = $this->driver;
        $previousMessage = $this->message;

        if ($driver instanceof CBot_Contract_DriverInterface) {
            $this->setDriver($driver);
        } elseif (\is_string($driver)) {
            $this->setDriver(CBot_DriverManager::loadFromName($driver, $this->config));
        }

        $recipients = \is_array($recipients) ? $recipients : [$recipients];

        foreach ($recipients as $recipient) {
            $this->message = new CBot_Message_Incoming_IncomingMessage('', $recipient, '', null, $this->config['bot_id'] ?? '');
            $response = $this->reply($message, $additionalParameters);
        }

        $this->message = $previousMessage;
        $this->driver = $previousDriver;

        return $response;
    }

    /**
     * @param string|CBot_Message_Outgoing_Question $question
     * @param array|Closure                         $next
     * @param array                                 $additionalParameters
     * @param null|string                           $recipient
     * @param null|string                           $driver
     *
     * @return CHTTP_Response
     */
    public function ask($question, $next, $additionalParameters = [], $recipient = null, $driver = null) {
        if (!\is_null($recipient) && !\is_null($driver)) {
            if (\is_string($driver)) {
                $driver = CBot_DriverManager::loadFromName($driver, $this->config);
            }
            $this->message = new CBot_Message_Incoming_IncomingMessage('', $recipient, '', null, $this->config['bot_id']);
            $this->setDriver($driver);
        }

        $response = $this->reply($question, $additionalParameters);
        $this->storeConversation(new CBot_Message_Conversation_InlineConversation(), $next, $question, $additionalParameters);

        return $response;
    }

    /**
     * @return $this
     */
    public function types() {
        $this->getDriver()->types($this->message);

        return $this;
    }

    /**
     * @param float $seconds Number of seconds to wait
     *
     * @return $this
     */
    public function typesAndWaits($seconds) {
        $this->getDriver()->typesAndWaits($this->message, $seconds);

        return $this;
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string $endpoint
     * @param array  $additionalParameters
     *
     * @throws BadMethodCallException
     *
     * @return $this
     */
    public function sendRequest($endpoint, $additionalParameters = []) {
        $driver = $this->getDriver();
        if (method_exists($driver, 'sendRequest')) {
            return $driver->sendRequest($endpoint, $additionalParameters, $this->message);
        }

        throw new CBot_Exception_BadMethodCallException('The driver ' . $this->getDriver()->getName() . ' does not support low level requests.');
    }

    /**
     * @param string|CBot_Message_Outgoing_OutgoingMessage|CBot_Message_Outgoing_Question $message
     * @param array                                                                       $additionalParameters
     *
     * @return mixed
     */
    public function reply($message, $additionalParameters = []) {
        $this->outgoingMessage = \is_string($message) ? CBot_Message_Outgoing_OutgoingMessage::create($message) : $message;

        return $this->sendPayload($this->getDriver()->buildServicePayload(
            $this->outgoingMessage,
            $this->message,
            $additionalParameters
        ));
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function sendPayload($payload) {
        return $this->middleware->applyMiddleware('sending', $payload, [], function ($payload) {
            $this->outgoingMessage = null;

            return $this->getDriver()->sendPayload($payload);
        });
    }

    /**
     * Return a random message.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function randomReply(array $messages) {
        return $this->reply($messages[array_rand($messages)]);
    }

    /**
     * Make an action for an invokable controller.
     *
     * @param string $action
     *
     * @throws CBot_Exception_UnexpectedValueException
     *
     * @return string
     */
    protected function makeInvokableAction($action) {
        if (!method_exists($action, '__invoke')) {
            throw new CBot_Exception_UnexpectedValueException(sprintf(
                'Invalid hears action: [%s]',
                $action
            ));
        }

        return $action . '@__invoke';
    }

    /**
     * @param mixed $callback
     *
     * @throws UnexpectedValueException
     * @throws NotFoundExceptionInterface
     *
     * @return mixed
     */
    protected function getCallable($callback) {
        if (is_callable($callback)) {
            return $callback;
        }

        if (strpos($callback, '@') === false) {
            $callback = $this->makeInvokableAction($callback);
        }

        list($class, $method) = explode('@', $callback);

        $command = new $class($this);

        return [$command, $method];
    }

    /**
     * @return array
     */
    public function getMatches() {
        return $this->matches;
    }

    /**
     * @return CBot_Message_Incoming_IncomingMessage
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return CBot_Message_Outgoing_OutgoingMessage|CBot_Message_Outgoing_Question
     */
    public function getOutgoingMessage() {
        return $this->outgoingMessage;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws CBot_Exception_BadMethodCallException
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (method_exists($this->getDriver(), $name)) {
            // Add the current message to the passed arguments
            $arguments[] = $this->getMessage();
            $arguments[] = $this;

            return \call_user_func_array([$this->getDriver(), $name], $arguments);
        }

        throw new CBot_Exception_BadMethodCallException('Method [' . $name . '] does not exist.');
    }

    /**
     * Load driver on wakeup.
     */
    public function __wakeup() {
        $this->driver = CBot_DriverManager::loadFromName($this->driverName, $this->config);
    }

    /**
     * @return array
     */
    public function __sleep() {
        $this->driverName = $this->driver->getName();

        return [
            'event',
            'exceptionHandler',
            'driverName',
            'storage',
            'message',
            'cache',
            'matches',
            'matcher',
            'config',
            'middleware',
        ];
    }
}
