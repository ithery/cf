<?php

class CBot_Message_Attachment_File extends CBot_Message_AttachmentAbstract {
    /**
     * Pattern that messages use to identify file uploads.
     */
    const PATTERN = '%%%_FILE_%%%';

    /**
     * @var string
     */
    protected $url;

    /**
     * Video constructor.
     *
     * @param string $url
     * @param mixed  $payload
     */
    public function __construct($url, $payload = null) {
        parent::__construct($payload);
        $this->url = $url;
    }

    /**
     * @param $url
     *
     * @return CBot_Message_Attachment_File
     */
    public static function url($url) {
        return new self($url);
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Get the instance as a web accessible array.
     * This will be used within the WebDriver.
     *
     * @return array
     */
    public function toWebDriver() {
        return [
            'type' => 'file',
            'url' => $this->url,
        ];
    }
}
