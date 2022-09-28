<?php
use Psr\Log\LoggerInterface;

class CServer_SMTP_ServerManager {
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AuthorizesRecipients
     */
    protected $recipientHandler;

    /**
     * @var Handler
     */
    protected $authHandler;

    /**
     * @var Server
     */
    protected $server;

    private $instance;

    /**
     * @return CServer_SMTP_ServerManager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Server constructor.
     *
     * @throws CContainer_Exception_BindingResolutionException
     */
    public function __construct() {
        $this->recipientHandler = c::container()->make(CF::config('server.smtpd.auth.authorize_recipients'));

        $this->logger = c::container()->make(Logger::class);

        if ($handler = CF::config('server.smtp.auth.handler')) {
            $this->authHandler = new $handler();
        }
        $this->authHandler = new CServer_SMTP_Auth_GuardHandler(CF::config('server.smtpd.auth.guard', 'smtp'));
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Run the server.
     *
     * @throws Exception
     */
    public function run() {
        $this
            ->makeServer()
            ->loop();
    }

    /**
     * Creates the server instance.
     *
     * @throws Exception
     *
     * @return CServer_SMTP_Server
     */
    protected function makeServer() {
        $this->server = new CServer_SMTP_Server($this->buildConfig());

        if (!$this->server->listen($this->buildContext())) {
            throw new Exception('SMTP Server could not listen on selected interface');
        }

        $this->server->addEvent(new CServer_SMTP_Event(
            CServer_SMTP_Event::TRIGGER_AUTH_ATTEMPT,
            $this,
            'handleAuthAttempt'
        ));

        $this->server->addEvent(new CServer_SMTP_Event(
            CServer_SMTP_Event::TRIGGER_NEW_MAIL,
            $this,
            'handleNewMail'
        ));

        $this->server->addEvent(new CServer_SMTP_Event(
            CServer_SMTP_Event::TRIGGER_NEW_RCPT,
            $this,
            'handleNewRecipient'
        ));

        return $this->server;
    }

    /**
     * Get the config for the server.
     *
     * @return array
     */
    protected function buildConfig() {
        return [
            'ip' => CF::config('server.smtp.interface'),
            'port' => CF::config('server.smtp.port'),
            'hostname' => CF::config('server.smtp.hostname'),
            'logger' => $this->logger,
        ];
    }

    /**
     * Get the context options.
     *
     * @return array
     */
    protected function buildContext() {
        $options = CF::config('server.smtp.context_options');
        if ($options && is_array($options)) {
            return $options;
        }

        return [];
    }

    /**
     * Handle an auth attempt.
     *
     * @param CServer_SMTP_Event $event
     * @param string             $method
     * @param array              $credentials
     *
     * @return bool
     */
    public function handleAuthAttempt(CServer_SMTP_Event $event, string $method, array $credentials) {
        try {
            switch ($method) {
                case 'login':
                    return !is_null($this->eventUser($event));
                default:
                    throw new Exception("Unsupported auth method '{$method}'.");
            }
        } catch (Exception $exception) {
            $this
                ->logger
                ->critical('Error while trying to authenticate.', compact('exception'));
        }

        return false;
    }

    /**
     * Try to get the user from an event.
     *
     * @param CServer_SMTP_Event $event
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    private function eventUser(CServer_SMTP_Event $event) {
        $credentials = $this
            ->authHandler
            ->decodeCredentials(
                $event
                    ->getClient()
                    ->getCredentials()
            );

        return $this
            ->authHandler
            ->attempt($credentials);
    }

    /**
     * Handle a new recipient added.
     *
     * @param CServer_SMTP_Event $event
     * @param string             $recipient
     *
     * @return bool
     */
    public function handleNewRecipient(CServer_SMTP_Event $event, $recipient) {
        if (!$this->recipientHandler) {
            return false;
        }

        return $this
            ->recipientHandler
            ->authorize($this->eventUser($event), $recipient);
    }

    /**
     * Handle an incoming message.
     *
     * @param CServer_SMTP_Event $event
     * @param string             $from
     * @param array              $recipients
     * @param string             $message
     *
     * @return bool
     */
    public function handleNewMail(CServer_SMTP_Event $event, string $from, array $recipients, string $message) {
        CServer_SMTP_Event_MessageReceived::dispatch($this->eventUser($event), CServer_SMTP_MessageFactory::make($message, $from, $recipients));
    }
}
