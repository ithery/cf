<?php

use Illuminate\Contracts\Support\Arrayable;

abstract class CApi_MethodAbstract implements Arrayable {
    /**
     * @var int
     */
    protected $errCode = 0;

    /**
     * @var string
     */
    protected $errMessage = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $request;

    /**
     * @var null|string
     */
    protected $lang = null;

    /**
     * @var null|string
     */
    protected $sessionId = null;

    protected $session;

    /**
     * @var null|CApi_HTTP_Request
     */
    protected $apiRequest;

    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    protected $sessionOptions = [
        'driver' => 'File',
        'expiration' => null,
    ];

    /**
     * @var int
     */
    protected $orgId;

    /**
     * @var string
     */
    protected $sessionIdParameter = 'sessionId';

    /**
     * @var string
     */
    protected $group;

    public function __construct($orgId = null, $sessionId = null, $request = null) {
        if ($orgId == null) {
            $orgId = CF::orgId();
        }
        $this->request = $request;

        $this->sessionId = $sessionId;
        $this->orgId = $orgId;
    }

    abstract public function execute();

    public function setApiRequest(CApi_HTTP_Request $apiRequest) {
        $this->apiRequest = $apiRequest;

        return $this;
    }

    public function setGroup($group) {
        $this->group = $group;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup() {
        return $this->group;
    }

    public function getApiRequest() {
        return $this->apiRequest;
    }

    /**
     * Register middleware on the controller.
     *
     * @param \Closure|array|string $middleware
     * @param array                 $options
     *
     * @return $this
     */
    public function middleware($middleware, array $options = []) {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return $this;
    }

    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware() {
        return $this->middleware;
    }

    public function toArray() {
        return $this->result();
    }

    public function request() {
        if ($this->request == null) {
            $request = [];
            if ($this->apiRequest && $this->apiRequest instanceof CApi_HTTP_Request) {
                $request = $this->apiRequest->all();
            }

            return array_merge($_GET, $_POST, $request);
        }

        return $this->request;
    }

    public function sessionId() {
        if ($this->sessionId == null) {
            $this->sessionId = carr::get($this->request(), $this->sessionIdParameter);
        }
        if ($this->sessionId == null) {
            $request = $this->apiRequest ?: c::request();
            $token = $request->bearerToken();
            if ($token) {
                $this->sessionId = $token;
            }
        }

        return $this->sessionId;
    }

    public function result() {
        $return = [
            'errCode' => (int) $this->errCode,
            'errMessage' => $this->errMessage,
            'data' => $this->data,
        ];

        return $return;
    }

    public function getErrCode() {
        return $this->errCode;
    }

    public function getErrMessage() {
        return $this->errMessage;
    }

    public function hasError() {
        return $this->errCode > 0;
    }

    public function lang($message, $params = []) {
        return c::__($message, $params, $this->lang);
    }

    /**
     * @return CApi_Session
     */
    public function session() {
        if ($this->session == null) {
            $this->session = $this->getSession();
        }

        return $this->session;
    }

    protected function getSession() {
        return CApi::session($this->sessionId(), $this->sessionOptions);
    }

    protected function validate($data, $rules, $messages = []) {
        if ($data == null) {
            $data = $this->request();
        }
        $validator = CValidation::createValidator($data, $rules, $messages);
        $validator->validate();
    }

    protected function manager() {
        return CApi_Manager::instance($this->group);
    }

    protected function auth() {
        return $this->manager()->auth();
    }
}
