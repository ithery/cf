<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 3:46:59 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGitlab_Client {

    /**
     * Constant for authentication method. Indicates the default, but deprecated
     * login with username and token in URL.
     */
    const AUTH_URL_TOKEN = 'url_token';

    /**
     * Constant for authentication method. Indicates the new login method with
     * with username and token via HTTP Authentication.
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * Constant for authentication method. Indicates the OAuth method with a key
     * obtain using Gitlab's OAuth provider.
     */
    const AUTH_OAUTH_TOKEN = 'oauth_token';

    /**
     * @var array
     */
    private $options = array(
        'user_agent' => 'php-gitlab-api',
        'timeout' => 60
    );
    /**
     *
     * @var string
     */
    protected $gitUrl;
    /**
     *
     * @var CCurl
     */
    protected $curl;
    protected $token;
    protected $authMethod;
    protected $sudo;

    public function __construct($gitUrl) {

        $this->gitUrl = $gitUrl;
        $this->curl = CCurl::factory($this->gitUrl);
        $this->curl->setTimeout(6000);
        $this->curl->setSSL();
    }

    public function authenticate($token, $authMethod = self::AUTH_URL_TOKEN, $sudo = null) {
        $this->token = $token;
        $this->authMethod = $authMethod;
        $this->sudo = $sudo;
        return $this;
    }

    public function api($name) {
        $version = strpos($this->gitUrl, '/api/v4/') ? 'CGitlab_Api' : 'CGitlab_ApiV3';
        $className = '';
        switch ($name) {
            case 'deploy_keys':
                $className = $version . '_DeployKeys';
                break;
            case 'groups':
                $className = $version . '_Groups';
                break;
            case 'issues':
                $className = $version . '_Issues';
                break;
            case 'mr':
            case 'merge_requests':
                $className = $version . '_MergeRequests';
                break;
            case 'milestones':
            case 'ms':
                $className = $version . '_Milestones';
                break;
            case 'namespaces':
            case 'ns':
                $className = $version . '_ProjectNamespaces';
                break;
            case 'projects':
                $className = $version . '_Projects';
                break;
            case 'repo':
            case 'repositories':
                $className = $version . '_Repositories';
                break;
            case 'snippets':
                $className = $version . '_Snippets';
                break;
            case 'hooks':
            case 'system_hooks':
                $className = $version . '_SystemHooks';
                break;
            case 'users':
                $className = $version . '_Users';
                break;
            default:
                throw new InvalidArgumentException('Invalid endpoint: "' . $name . '"');
        }
        $api = new $className($this);
        return $api;
    }

    /**
     * {@inheritDoc}
     */
    protected function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array(), array $files = array()) {
        $path = trim($this->gitUrl . $path, '/');

        // Skip by default
        if (null === $this->authMethod) {
            return;
        }

        $headers = array();
        switch ($this->authMethod) {
            case self::AUTH_HTTP_TOKEN:
                $headers[] = 'PRIVATE-TOKEN: ' . $this->token;
                if (!is_null($this->sudo)) {
                    $headers[] = 'SUDO: ' . $this->sudo;
                }
                break;
            case self::AUTH_URL_TOKEN:
                $url = $path;
                $query = array(
                    'private_token' => $this->token
                );
                if (!is_null($this->sudo)) {
                    $query['sudo'] = $this->sudo;
                }
                $url .= (false === strpos($url, '?') ? '?' : '&') . utf8_encode(http_build_query($query, '', '&'));
                $path = $url;
                break;
            case self::AUTH_OAUTH_TOKEN:
                $headers[] = 'Authorization: Bearer ' . $this->token;
                if (!is_null($this->sudo)) {
                    $headers[] = 'SUDO: ' . $this->sudo;
                }

                break;
        }

        $this->curl->setUrl($path);
       
        $this->curl->setOpt(CURLOPT_HTTPHEADER, $headers);

        $response = $this->curl->exec()->response();



        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array()) {
        if (0 < count($parameters)) {
            $path .= (false === strpos($path, '?') ? '?' : '&') . http_build_query($parameters, '', '&');
        }
        return $this->request($path, array(), 'GET', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), array $headers = array(), array $files = array()) {
        return $this->request($path, $parameters, 'POST', $headers, $files);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, array $parameters = array(), array $headers = array()) {
        return $this->request($path, $parameters, 'PATCH', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), array $headers = array()) {
        return $this->request($path, $parameters, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $parameters = array(), array $headers = array()) {
        return $this->request($path, $parameters, 'PUT', $headers);
    }

}
