<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 9:32:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Remote_Client_Engine_Server extends CApp_Remote_Client_Engine {

    public function __construct($options) {
        parent::__construct($options);
        $this->baseUrl .= 'Server/';
    }

    public function getPHPInfo() {
        $data = $this->request($this->baseUrl . 'GetPHPInfo');
        return $data;
    }

    public function getDomainList() {
        $data = $this->request($this->baseUrl . 'GetDomainList');
        return $data;
    }

    public function getDomainInfo($domain) {
        $post = array();
        $post['domain'] = $domain;
        $data = $this->request($this->baseUrl . 'GetDomainInfo', $post);
        return $data;
    }

    public function getServerInfo() {
        $data = $this->request($this->baseUrl . 'GetServerInfo');
        return $data;
    }

    public function getServerStorageInfo() {
        $data = $this->request($this->baseUrl . 'GetServerStorageInfo');
        return $data;
    }

    public function deleteDomain($domain) {
        $post = array();
        $post['domain'] = $domain;
        $data = $this->request($this->baseUrl . 'DomainDelete', $post);
        return $data;
    }

    public function getFileList($directory) {
        $post = array();
        $post['directory'] = $directory;
        $data = $this->request($this->baseUrl . 'GetFileList', $post);
        return $data;
    }

    public function temp($post) {
        $data = $this->request($this->baseUrl . 'Temp', $post);
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
