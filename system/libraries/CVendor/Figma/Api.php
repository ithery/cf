<?php

class CVendor_Figma_Api {
    protected $accessToken;

    protected $options;

    /**
     * @var CVendor_Figma_Client
     */
    protected $client;

    public function __construct($accessToken, $options = []) {
        $this->accessToken = $accessToken;
        $this->options = $options;
        $this->client = new CVendor_Figma_Client(new CVendor_Figma_Adapter_GuzzleAdapter(['accessToken' => $this->accessToken] + $options), $this->getBaseUri());
    }

    public function extractFileKeyFromUrl($url) {
        $pattern = "/file\/([a-zA-Z0-9]+)\//";
        preg_match($pattern, $url, $matches);

        return $matches[1] ?? null;
    }

    /**
     * @return string
     */
    public function getBaseUri() {
        return carr::get($this->options, 'baseUri', 'https://api.figma.com/v1/');
    }

    public function getAccountInfo() {
        return $this->handleResponse($this->client->get('me'));
    }

    public function getProjectsInTeam($teamId) {
        return $this->handleResponse($this->client->get("teams/{$teamId}/projects"));
    }

    public function getFilesInProject($projectId) {
        return $this->handleResponse($this->client->get("projects/{$projectId}/files"));
    }

    public function getFileInfo($fileKey) {
        return $this->handleResponse($this->client->get("files/{$fileKey}"));
    }

    /**
     * @param mixed $response
     *
     * @return array
     */
    public function handleResponse($response) {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        return $response;
    }
}
