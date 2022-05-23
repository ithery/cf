<?php
if (!is_callable('random_bytes')) {
    require_once DOCROOT . 'system/vendor/random_compat/random.php';
}
class CException_Report {
    use CException_Concern_UseTimeTrait;
    use CException_Concern_HasContextTrait;

    /**
     * @var null string|null
     */
    public static $fakeTrackingUuid = null;

    /**
     * @var \CException_Stacktrace
     */
    private $stacktrace;

    /**
     * @var string
     */
    private $exceptionClass;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $glows = [];

    /**
     * @var array
     */
    private $solutions = [];

    /**
     * @var CException_Contract_ContextInterface
     */
    private $context;

    /**
     * @var string
     */
    private $applicationPath;

    /**
     * @var ?string
     */
    private $applicationVersion;

    /**
     * @var array
     */
    private $exceptionContext = [];

    /**
     * @var Throwable
     */
    private $throwable;

    /**
     * @var string
     */
    private $notifierName;

    /**
     * @var string
     */
    private $languageVersion;

    /**
     * @var string
     */
    private $frameworkVersion;

    /**
     * @var int
     */
    private $openFrameIndex;

    /**
     * @var string
     */
    private $groupBy;

    /**
     * @var string
     */
    private $trackingUuid;

    /**
     * Undocumented function.
     *
     * @param [type] $throwable
     * @param CException_Contract_ContextInterface $context
     * @param [type] $applicationPath
     * @param [type] $version
     *
     * @return CException_Report
     */
    public static function createForThrowable(
        $throwable,
        CException_Contract_ContextInterface $context,
        $applicationPath = null,
        $version = null
    ) {
        return (new static())
            ->throwable($throwable)
            ->useContext($context)
            ->exceptionClass(self::getClassForThrowable($throwable))
            ->message($throwable->getMessage())
            ->stackTrace(CException_Stacktrace::createForThrowable($throwable, $applicationPath))
            ->exceptionContext($throwable);
    }

    protected static function getClassForThrowable($throwable) {
        if ($throwable instanceof CView_Exception_ViewException) {
            if ($previous = $throwable->getPrevious()) {
                return get_class($previous);
            }
        }

        return get_class($throwable);
    }

    public static function createForMessage($message, $logLevel, CException_Contract_ContextInterface $context) {
        $stacktrace = CException_Stacktrace::create(c::docRoot());

        return (new static())
            ->message($message)
            ->useContext($context)
            ->exceptionClass($logLevel)
            ->stacktrace($stacktrace)
            ->openFrameIndex($stacktrace->firstApplicationFrameIndex());
    }

    public function allContext() {
        $context = $this->context->toArray();

        $context = carr::arrayMergeRecursiveDistinct($context, $this->exceptionContext);

        return carr::arrayMergeRecursiveDistinct($context, $this->userProvidedContext);
    }

    public function __construct() {
        $this->applicationPath = c::docRoot();
        $this->trackingUuid = self::$fakeTrackingUuid ?: $this->generateUuid();
        $this->applicationVersion = '';
        $this->frameworkVersion = CF::version();
    }

    /**
     * @param null|int $index
     *
     * @return $this
     */
    public function openFrameIndex($index) {
        $this->openFrameIndex = $index;

        return $this;
    }

    public function trackingUuid() {
        return $this->trackingUuid;
    }

    /**
     * @param string $exceptionClass
     *
     * @return $this
     */
    public function exceptionClass($exceptionClass) {
        $this->exceptionClass = $exceptionClass;

        return $this;
    }

    public function getExceptionClass() {
        return $this->exceptionClass;
    }

    /**
     * @param Throwable $throwable
     *
     * @return $this
     */
    public function throwable($throwable) {
        $this->throwable = $throwable;

        return $this;
    }

    /**
     * @return null|Throwable
     */
    public function getThrowable() {
        return $this->throwable;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function message($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    public function useContext(CException_Contract_ContextInterface $request) {
        $this->context = $request;

        return $this;
    }

    /**
     * @param array $userProvidedContext
     *
     * @return $this
     */
    public function userProvidedContext(array $userProvidedContext) {
        $this->userProvidedContext = $userProvidedContext;

        return $this;
    }

    public function toArray() {
        return [
            'notifier' => $this->notifierName ?: 'CF Client',
            'language' => 'PHP',
            'framework_version' => $this->frameworkVersion,
            'language_version' => $this->languageVersion ?: phpversion(),
            'exception_class' => $this->exceptionClass,
            'seen_at' => $this->getCurrentTime(),
            'message' => $this->message,
            'glows' => $this->glows,
            'solutions' => $this->solutions,
            'stacktrace' => $this->stacktrace->toArray(),
            'context' => $this->allContext(),
            'stage' => $this->stage,
            'message_level' => $this->messageLevel,
            'open_frame_index' => $this->openFrameIndex,
            'application_path' => $this->applicationPath,
            'application_version' => $this->applicationVersion,
            'tracking_uuid' => $this->trackingUuid,
        ];
    }

    /**
     * @param CException_Stacktrace $stacktrace
     *
     * @return $this
     */
    public function stacktrace(CException_Stacktrace $stacktrace) {
        $this->stacktrace = $stacktrace;

        return $this;
    }

    /**
     * @return CException_Stacktrace
     */
    public function getStacktrace() {
        return $this->stacktrace;
    }

    public function notifierName($notifierName) {
        $this->notifierName = $notifierName;

        return $this;
    }

    private function exceptionContext($throwable) {
        if ($throwable instanceof CException_Contract_ProvidesContextInterface) {
            $this->exceptionContext = $throwable->context();
        }

        return $this;
    }

    /**
     * Found on https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555.
     *
     * @return string
     */
    private function generateUuid() {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
