<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a Attachment object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_Attachment implements \JsonSerializable {

    /** @var $content string Base64 encoded content */
    private $content;

    /** @var $type string Mime type of the attachment */
    private $type;

    /** @var $filename string File name of the attachment */
    private $filename;

    /** @var $disposition string How the attachment should be displayed: inline or attachment, default is attachment */
    private $disposition;

    /** @var $content_id string Used when disposition is inline to diplay the file within the body of the email */
    private $content_id;

    /**
     * Optional constructor
     *
     * @param string $content Base64 encoded content
     * @param string $type Mime type of the attachment
     * @param string $filename File name of the attachment
     * @param string $disposition How the attachment should be displayed: inline
     *                            or attachment, default is attachment
     * @param string $content_id Used when disposition is inline to diplay the
     *                            file within the body of the email
     */
    public function __construct(
    $content = null, $type = null, $filename = null, $disposition = null, $content_id = null
    ) {
        if (isset($content)) {
            $this->setContent($content);
        }
        if (isset($type)) {
            $this->setType($type);
        }
        if (isset($filename)) {
            $this->setFilename($filename);
        }
        if (isset($disposition)) {
            $this->setDisposition($disposition);
        }
        if (isset($content_id)) {
            $this->setContentID($content_id);
        }
    }

    /**
     * Add the content to a Attachment object
     *
     * @param string $content Base64 encoded content
     *
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setContent($content) {
        if (!is_string($content)) {
            throw new CVendor_SendGrid_Mail_TypeException('$content must be of type string.');
        }
        if (!$this->isBase64($content)) {
            $this->content = base64_encode($content);
        } else {
            $this->content = $content;
        }
    }

    /**
     * Retrieve the content from a Attachment object
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Add the mime type to a Attachment object
     *
     * @param string $type Mime type of the attachment
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setType($type) {
        if (!is_string($type)) {
            throw new CVendor_SendGrid_Mail_TypeException('$type must be of type string.');
        }
        $this->type = $type;
    }

    /**
     * Retrieve the mime type from a Attachment object
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Add the file name to a Attachment object
     *
     * @param string $filename File name of the attachment
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setFilename($filename) {
        if (!is_string($filename)) {
            throw new CVendor_SendGrid_Mail_TypeException('$filename must be of type string');
        }
        $this->filename = $filename;
    }

    /**
     * Retrieve the file name from a Attachment object
     *
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Add the disposition to a Attachment object
     *
     * @param string $disposition How the attachment should be displayed:
     *                            inline or attachment, default is attachment
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setDisposition($disposition) {
        if (!is_string($disposition)) {
            throw new CVendor_SendGrid_Mail_TypeException('$disposition must be of type string.');
        }
        $this->disposition = $disposition;
    }

    /**
     * Retrieve the disposition from a Attachment object
     *
     * @return string
     */
    public function getDisposition() {
        return $this->disposition;
    }

    /**
     * Add the content id to a Attachment object
     *
     * @param string $content_id Used when disposition is inline to diplay
     *                           the file within the body of the email
     */
    public function setContentID($content_id) {
        if (!is_string($content_id)) {
            throw new CVendor_SendGrid_Mail_TypeException('$content_id must be of type string.');
        }
        $this->content_id = $content_id;
    }

    /**
     * Retrieve the content id from a Attachment object
     *
     * @return string
     */
    public function getContentID() {
        return $this->content_id;
    }

    /**
     *  Verifies whether or not the provided string is a valid base64 string
     *
     * @param $string string The string that has to be checked
     * @return bool
     */
    private function isBase64($string) {
        $decoded_data = base64_decode($string, true);
        $encoded_data = base64_encode($decoded_data);
        if ($encoded_data != $string) {
            return false;
        }
        return true;
    }

    /**
     * Return an array representing a Attachment object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'content' => $this->getContent(),
                    'type' => $this->getType(),
                    'filename' => $this->getFilename(),
                    'disposition' => $this->getDisposition(),
                    'content_id' => $this->getContentID()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
