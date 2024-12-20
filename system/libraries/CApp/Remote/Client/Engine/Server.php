<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 9:32:42 PM
 */
class CApp_Remote_Client_Engine_Server extends CApp_Remote_Client_Engine {
    public function __construct($options) {
        parent::__construct($options);
        $this->baseApiUrl .= 'Server/';
    }

    public function getPHPInfo() {
        $data = $this->request($this->baseApiUrl . 'GetPHPInfo');

        return $data;
    }

    public function getDomainList() {
        $data = $this->request($this->baseApiUrl . 'GetDomainList');

        return $data;
    }

    public function getDomainInfo($domain) {
        $post = [];
        $post['domain'] = $domain;
        $data = $this->request($this->baseApiUrl . 'GetDomainInfo', $post);

        return $data;
    }

    public function getServerInfo() {
        $data = $this->request($this->baseApiUrl . 'GetServerInfo');

        return $data;
    }

    public function getServerStorageInfo() {
        $data = $this->request($this->baseApiUrl . 'GetServerStorageInfo');

        return $data;
    }

    public function deleteDomain($domain) {
        $post = [];
        $post['domain'] = $domain;
        $data = $this->request($this->baseApiUrl . 'DomainDelete', $post);

        return $data;
    }

    public function getFileList($directory) {
        $post = [];
        $post['directory'] = $directory;
        $data = $this->request($this->baseApiUrl . 'GetFileList', $post);

        return $data;
    }

    public function temp($post) {
        $data = $this->request($this->baseApiUrl . 'Temp', $post);

        return $data;
    }

    public function tempFileList($directory) {
        $post = [];
        $post['command'] = 'listFile';
        $post['directory'] = $directory;

        return $this->temp($post);
    }

    public function tempContent($file) {
        $post = [];
        $post['command'] = 'content';
        $post['file'] = $file;

        return $this->temp($post);
    }

    public function tempDelete($file) {
        $post = [];
        $post['command'] = 'content';
        $post['file'] = $file;

        return $this->temp($post);
    }
}
