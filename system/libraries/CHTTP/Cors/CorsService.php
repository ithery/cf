<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://github.com/asm89/stack-cors
 */
class CHTTP_Cors_CorsService {
    private $options;

    public function __construct(array $options = []) {
        $this->options = $this->normalizeOptions($options);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function normalizeOptions(array $options = []) {
        $options += [
            'allowedOrigins' => [],
            'allowedOriginsPatterns' => [],
            'supportsCredentials' => false,
            'allowedHeaders' => [],
            'exposedHeaders' => [],
            'allowedMethods' => [],
            'maxAge' => 0,
        ];

        // normalize array('*') to true
        if (in_array('*', $options['allowedOrigins'])) {
            $options['allowedOrigins'] = true;
        }
        if (in_array('*', $options['allowedHeaders'])) {
            $options['allowedHeaders'] = true;
        } else {
            $options['allowedHeaders'] = array_map('strtolower', $options['allowedHeaders']);
        }

        if (in_array('*', $options['allowedMethods'])) {
            $options['allowedMethods'] = true;
        } else {
            $options['allowedMethods'] = array_map('strtoupper', $options['allowedMethods']);
        }

        return $options;
    }

    /**
     * @param Request $request
     *
     * @deprecated use isOriginAllowed
     *
     * @return bool
     */
    public function isActualRequestAllowed(Request $request) {
        return $this->isOriginAllowed($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isCorsRequest(Request $request) {
        return $request->headers->has('Origin');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isPreflightRequest(Request $request) {
        return $request->getMethod() === 'OPTIONS' && $request->headers->has('Access-Control-Request-Method');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handlePreflightRequest(Request $request) {
        $response = new Response();

        $response->setStatusCode(204);

        return $this->addPreflightRequestHeaders($response, $request);
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return Response
     */
    public function addPreflightRequestHeaders(Response $response, Request $request) {
        $this->configureAllowedOrigin($response, $request);

        if ($response->headers->has('Access-Control-Allow-Origin')) {
            $this->configureAllowCredentials($response, $request);

            $this->configureAllowedMethods($response, $request);

            $this->configureAllowedHeaders($response, $request);

            $this->configureMaxAge($response, $request);
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isOriginAllowed(Request $request) {
        if ($this->options['allowedOrigins'] === true) {
            return true;
        }

        if (!$request->headers->has('Origin')) {
            return false;
        }

        $origin = $request->headers->get('Origin');

        if (in_array($origin, $this->options['allowedOrigins'])) {
            return true;
        }

        foreach ($this->options['allowedOriginsPatterns'] as $pattern) {
            if (preg_match($pattern, $origin)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return Response
     */
    public function addActualRequestHeaders(Response $response, Request $request) {
        $this->configureAllowedOrigin($response, $request);

        if ($response->headers->has('Access-Control-Allow-Origin')) {
            $this->configureAllowCredentials($response, $request);

            $this->configureExposedHeaders($response, $request);
        }

        return $response;
    }

    private function configureAllowedOrigin(Response $response, Request $request) {
        if ($this->options['allowedOrigins'] === true && !$this->options['supportsCredentials']) {
            // Safe+cacheable, allow everything
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } elseif ($this->isSingleOriginAllowed()) {
            // Single origins can be safely set
            $response->headers->set('Access-Control-Allow-Origin', array_values($this->options['allowedOrigins'])[0]);
        } else {
            // For dynamic headers, set the requested Origin header when set and allowed
            if ($this->isCorsRequest($request) && $this->isOriginAllowed($request)) {
                $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
            }

            $this->varyHeader($response, 'Origin');
        }
    }

    /**
     * @return bool
     */
    private function isSingleOriginAllowed() {
        if ($this->options['allowedOrigins'] === true || !empty($this->options['allowedOriginsPatterns'])) {
            return false;
        }

        return count($this->options['allowedOrigins']) === 1;
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return void
     */
    private function configureAllowedMethods(Response $response, Request $request) {
        if ($this->options['allowedMethods'] === true) {
            $allowMethods = strtoupper($request->headers->get('Access-Control-Request-Method'));
            $this->varyHeader($response, 'Access-Control-Request-Method');
        } else {
            $allowMethods = implode(', ', $this->options['allowedMethods']);
        }

        $response->headers->set('Access-Control-Allow-Methods', $allowMethods);
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return void
     */
    private function configureAllowedHeaders(Response $response, Request $request) {
        if ($this->options['allowedHeaders'] === true) {
            $allowHeaders = $request->headers->get('Access-Control-Request-Headers');
            $this->varyHeader($response, 'Access-Control-Request-Headers');
        } else {
            $allowHeaders = implode(', ', $this->options['allowedHeaders']);
        }
        $response->headers->set('Access-Control-Allow-Headers', $allowHeaders);
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return void
     */
    private function configureAllowCredentials(Response $response, Request $request) {
        if ($this->options['supportsCredentials']) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return void
     */
    private function configureExposedHeaders(Response $response, Request $request) {
        if ($this->options['exposedHeaders']) {
            $response->headers->set('Access-Control-Expose-Headers', implode(', ', $this->options['exposedHeaders']));
        }
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return void
     */
    private function configureMaxAge(Response $response, Request $request) {
        if ($this->options['maxAge'] !== null) {
            $response->headers->set('Access-Control-Max-Age', (int) $this->options['maxAge']);
        }
    }

    /**
     * @param Response $response
     * @param string   $header
     *
     * @return Response
     */
    public function varyHeader(Response $response, $header): Response {
        if (!$response->headers->has('Vary')) {
            $response->headers->set('Vary', $header);
        } elseif (!in_array($header, explode(', ', $response->headers->get('Vary')))) {
            $response->headers->set('Vary', $response->headers->get('Vary') . ', ' . $header);
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isSameHost(Request $request) {
        return $request->headers->get('Origin') === $request->getSchemeAndHttpHost();
    }
}
