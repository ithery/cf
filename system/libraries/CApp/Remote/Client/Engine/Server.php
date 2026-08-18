<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Remote_Client_Engine_Server extends CApp_Remote_Client_Engine {
    /**
     * @param array $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->baseApiUrl .= 'Server/';
    }

    /**
     * @return mixed
     */
    public function getPHPInfo() {
        $data = $this->request($this->baseApiUrl . 'GetPHPInfo');

        return $data;
    }

    /**
     * @return mixed
     */
    public function getDomainList() {
        $data = $this->request($this->baseApiUrl . 'GetDomainList');

        return $data;
    }

    /**
     * @param string $domain
     *
     * @return mixed
     */
    public function getDomainInfo($domain) {
        $post = [];
        $post['domain'] = $domain;
        $data = $this->request($this->baseApiUrl . 'GetDomainInfo', $post);

        return $data;
    }

    /**
     * @return mixed
     */
    public function getServerInfo() {
        $data = $this->request($this->baseApiUrl . 'GetServerInfo');

        return $data;
    }

    /**
     * @return mixed
     */
    public function getServerStorageInfo() {
        $data = $this->request($this->baseApiUrl . 'GetServerStorageInfo');

        return $data;
    }

    /**
     * @param string $domain
     *
     * @return mixed
     */
    public function deleteDomain($domain) {
        $post = [];
        $post['domain'] = $domain;
        $data = $this->request($this->baseApiUrl . 'DomainDelete', $post);

        return $data;
    }

    /**
     * @param string $directory
     *
     * @return mixed
     */
    public function getFileList($directory) {
        $post = [];
        $post['directory'] = $directory;
        $data = $this->request($this->baseApiUrl . 'GetFileList', $post);

        return $data;
    }

    /**
     * @param array $post
     *
     * @return mixed
     */
    public function temp($post) {
        $data = $this->request($this->baseApiUrl . 'Temp', $post);

        return $data;
    }

    /**
     * @param string $directory
     *
     * @return mixed
     */
    public function tempFileList($directory) {
        $post = [];
        $post['command'] = 'listFile';
        $post['directory'] = $directory;

        return $this->temp($post);
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    public function tempContent($file) {
        $post = [];
        $post['command'] = 'content';
        $post['file'] = $file;

        return $this->temp($post);
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    public function tempDelete($file) {
        $post = [];
        $post['command'] = 'content';
        $post['file'] = $file;

        return $this->temp($post);
    }
}
