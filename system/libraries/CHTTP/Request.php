<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 10:24:00 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class CHTTP_Request extends SymfonyRequest implements CInterface_Arrayable, ArrayAccess {

    use CHTTP_Trait_InteractsWithInput,
        CHTTP_Trait_InteractsWithContentTypes;

    /**
     * The decoded JSON content for the request.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag|null
     */
    protected $json;

    /**
     * All of the converted files for the request.
     *
     * @var array
     */
    protected $convertedFiles;

    /**
     * Create an object request from a Symfony instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return CHTTP_Request
     */
    public static function createFromBase(SymfonyRequest $request) {
        if ($request instanceof static) {
            return $request;
        }

        $content = $request->content;

        $request = (new static)->duplicate(
                $request->query->all(), $request->request->all(), $request->attributes->all(), $request->cookies->all(), $request->files->all(), $request->server->all()
        );

        $request->content = $content;

        $request->request = $request->getInputSource();

        return $request;
    }

    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource() {
        if ($this->isJson()) {
            return $this->json();
        }

        return $this->getRealMethod() == 'GET' ? $this->query : $this->request;
    }

    /**
     * Create a new Illuminate HTTP request from server variables.
     *
     * @return static
     */
    public static function capture() {
        static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method() {
        return $this->getMethod();
    }

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path() {
        $pattern = trim($this->getPathInfo(), '/');

        return $pattern == '' ? '/' : $pattern;
    }

    /**
     * Get the JSON payload for the request.
     *
     * @param  string|null  $key
     * @param  mixed   $default
     * @return \Symfony\Component\HttpFoundation\ParameterBag|mixed
     */
    public function json($key = null, $default = null) {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array) json_decode($this->getContent(), true));
        }
        if (is_null($key)) {
            return $this->json;
        }
        return carr::get($this->json->all(), $key, $default);
    }

    /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax() {
        return $this->isXmlHttpRequest();
    }

    /**
     * Determine if the request is the result of an PJAX call.
     *
     * @return bool
     */
    public function pjax() {
        return $this->headers->get('X-PJAX') == true;
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function toArray() {
        return $this->all();
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return array_key_exists(
                $offset, $this->all() + $this->route()->parameters()
        );
    }

    /**
     * Get the value at the given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->__get($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value) {
        $this->getInputSource()->set($offset, $value);
    }

    /**
     * Remove the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset) {
        $this->getInputSource()->remove($offset);
    }

    /**
     * Check if an input element is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key) {
        return !is_null($this->__get($key));
    }

    /**
     * Get an input element from the request.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {

        if (array_key_exists($key, $this->all())) {
            return carr::get($this->all(), $key);
        }
        return null;
        //return $this->route($key);
    }

    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root() {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }

}
