<?php

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class CApi_HTTP_Response extends CHTTP_Response {
    /**
     * The exception that triggered the error response.
     *
     * @var \Exception
     */
    public $exception;

    /**
     * @var string
     */
    protected $group;

    /**
     * Transformer binding instance.
     *
     * @var \CApi_Transformer_Binding
     */
    protected $binding;

    /**
     * Array of registered formatters.
     *
     * @var array
     */
    protected $formatters = [
        'json' => CApi_HTTP_Response_Format_JsonFormat::class,
        'jsonp' => CApi_HTTP_Response_Format_JsonFormat::class,
        'default' => CApi_HTTP_Response_Format_DefaultFormat::class,

    ];

    /**
     * Array of formats' options.
     *
     * @var array
     */
    protected $formatsOptions = [

        'json' => [
            'pretty_print' => false,
            'indent_style' => 'space',
            'indent_size' => 2,
        ],
        'default' => [
            'pretty_print' => false,
            'indent_style' => 'space',
            'indent_size' => 2,
        ],

    ];

    /**
     * Transformer factory instance.
     *
     * @var \CApi_Transformer_Factory
     */
    protected $transformer;

    /**
     * Create a new response instance.
     *
     * @param mixed                     $content
     * @param int                       $status
     * @param array                     $headers
     * @param \CApi_Transformer_Binding $binding
     *
     * @return void
     */
    public function __construct($content, $status = 200, $headers = [], CApi_Transformer_Binding $binding = null) {
        parent::__construct($content, $status, $headers);

        $this->binding = $binding;
    }

    /**
     * @param string $group
     *
     * @return $this
     */
    public function setGroup($group) {
        $this->group = $group;

        return $this;
    }

    /**
     * Make an API response from an existing HTTP response.
     *
     * @param \CHTTP_Response $old
     *
     * @return \CApi_HTTP_Response
     */
    public static function makeFromExisting(CHTTP_Response $old) {
        $new = new static($old->getOriginalContent(), $old->getStatusCode());

        $new->headers = $old->headers;

        return $new;
    }

    /**
     * Make an API response from an existing JSON response.
     *
     * @param \CHTTP_JsonResponse $json
     *
     * @return \CApi_HTTP_Response
     */
    public static function makeFromJson(CHTTP_JsonResponse $json) {
        $content = $json->getContent();

        // If the contents of the JsonResponse does not starts with /**/ (typical laravel jsonp response)
        // we assume that it is a valid json response that can be decoded, or we just use the raw jsonp
        // contents for building the response
        if (!cstr::startsWith($json->getContent(), '/**/')) {
            $content = json_decode($json->getContent(), true);
        }

        $new = new static($content, $json->getStatusCode());

        $new->headers = $json->headers;

        return $new;
    }

    /**
     * Morph the API response to the appropriate format.
     *
     * @param string $format
     *
     * @return \CApi_HTTP_Response
     */
    public function morph($format = 'json') {
        $formatter = c::api($this->group)->resultFormatter();
        $transformer = c::api($this->group)->transformer();
        $this->content = $this->getOriginalContent();

        $this->fireMorphingEvent();

        if (isset($transformer) && $transformer->transformableResponse($this->content)) {
            $this->content = $transformer->transform($this->content);
        }

        $formatter->setOptions($this->getFormatsOptions($format));

        $defaultContentType = $this->headers->get('Content-Type');

        // If we have no content, we don't want to set this header, as it will be blank
        $contentType = $formatter->getContentType();
        if (!empty($contentType)) {
            $this->headers->set('Content-Type', $formatter->getContentType());
        }

        $this->fireMorphedEvent();

        if ($this->content instanceof CModel) {
            $this->content = $formatter->formatModel($this->content);
        } elseif ($this->content instanceof CModel_Collection) {
            $this->content = $formatter->formatModelCollection($this->content);
        } elseif (is_array($this->content) || $this->content instanceof ArrayObject || $this->content instanceof Arrayable) {
            $this->content = $formatter->formatArray($this->content);
        } else {
            if (!empty($defaultContentType)) {
                $this->headers->set('Content-Type', $defaultContentType);
            }
        }

        return $this;
    }

    /**
     * Fire the morphed event.
     *
     * @return void
     */
    protected function fireMorphedEvent() {
        if (!$this->events()) {
            return;
        }

        $this->events()->dispatch(new CApi_Event_ResponseWasMorphed($this, $this->content));
    }

    /**
     * @return CEvent_Dispatcher
     */
    protected function events() {
        return CEvent::dispatcher();
    }

    /**
     * Fire the morphing event.
     *
     * @return void
     */
    protected function fireMorphingEvent() {
        if (!$this->events()) {
            return;
        }

        $this->events()->dispatch(new CApi_Event_ResponseIsMorphing($this, $this->content));
    }

    /**
     * @inheritdoc
     */
    public function setContent($content) {
        // Attempt to set the content string, if we encounter an unexpected value
        // then we most likely have an object that cannot be type cast. In that
        // case we'll simply leave the content as null and set the original
        // content value and continue.
        if (!empty($content) && is_object($content) && !$this->shouldBeJson($content)) {
            $this->original = $content;

            return $this;
        }

        try {
            return parent::setContent($content);
        } catch (UnexpectedValueException $exception) {
            $this->original = $content;

            return $this;
        }
    }

    /**
     * Get the formatter based on the requested format type.
     *
     * @param string $format
     *
     * @throws \RuntimeException
     *
     * @return \CApi_HTTP_Response_FormatAbstract
     */
    public function getFormatter($format) {
        if (!$this->hasFormatter($format)) {
            throw new NotAcceptableHttpException('Unable to format response according to Accept header.');
        }

        return $this->formatters[$format];
    }

    /**
     * Determine if a response formatter has been registered.
     *
     * @param string $format
     *
     * @return bool
     */
    public function hasFormatter($format) {
        return isset($this->formatters[$format]);
    }

    /**
     * Set the response formatters.
     *
     * @param array $formatters
     *
     * @return void
     */
    public function addFormatters(array $formatters) {
        $this->formatters = array_merge($this->formatters, $formatters);
    }

    /**
     * Set the formats' options.
     *
     * @param array $formatsOptions
     *
     * @return void
     */
    public function addFormatsOptions(array $formatsOptions) {
        $this->formatsOptions = array_merge($this->formatsOptions, $formatsOptions);
    }

    /**
     * Get the format's options.
     *
     * @param string $format
     *
     * @return array
     */
    public function getFormatsOptions($format) {
        if (!$this->hasOptionsForFormat($format)) {
            return [];
        }

        return $this->formatsOptions[$format];
    }

    /**
     * Determine if any format's options were set.
     *
     * @param string $format
     *
     * @return bool
     */
    public function hasOptionsForFormat($format) {
        return isset($this->formatsOptions[$format]);
    }

    /**
     * Add a response formatter.
     *
     * @param string                             $key
     * @param \CApi_HTTP_Response_FormatAbstract $formatter
     *
     * @return void
     */
    public function addFormatter($key, $formatter) {
        $this->formatters[$key] = $formatter;
    }

    /**
     * Set the transformer factory instance.
     *
     * @param \CApi_Transformer_Factory $transformer
     *
     * @return void
     */
    public function setTransformer(CApi_Transformer_Factory $transformer) {
        $this->transformer = $transformer;
    }

    /**
     * Get the transformer instance.
     *
     * @return \CApi_Transformer_Factory
     */
    public function getTransformer() {
        return $this->transformer;
    }

    /**
     * Add a meta key and value pair.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \CApi_HTTP_Response
     */
    public function addMeta($key, $value) {
        $this->binding->addMeta($key, $value);

        return $this;
    }

    /**
     * Add a meta key and value pair.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \CApi_HTTP_Response
     */
    public function meta($key, $value) {
        return $this->addMeta($key, $value);
    }

    /**
     * Set the meta data for the response.
     *
     * @param array $meta
     *
     * @return \CApi_HTTP_Response
     */
    public function setMeta(array $meta) {
        $this->binding->setMeta($meta);

        return $this;
    }

    /**
     * Get the meta data for the response.
     *
     * @return array
     */
    public function getMeta() {
        return $this->binding->getMeta();
    }

    /**
     * Add a cookie to the response.
     *
     * @param \Symfony\Component\HttpFoundation\Cookie|mixed $cookie
     *
     * @return \CApi_HTTP_Response
     */
    public function cookie($cookie) {
        return $this->withCookie($cookie);
    }

    /**
     * Add a header to the response.
     *
     * @param string $key
     * @param string $value
     * @param bool   $replace
     *
     * @return \CApi_HTTP_Response
     */
    public function withHeader($key, $value, $replace = true) {
        return $this->header($key, $value, $replace);
    }

    /**
     * Set the response status code.
     *
     * @param int $statusCode
     *
     * @return \CApi_HTTP_Response
     */
    public function statusCode($statusCode) {
        return $this->setStatusCode($statusCode);
    }
}
