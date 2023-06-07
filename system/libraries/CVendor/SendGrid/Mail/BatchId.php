<?php

/**
 * This class is used to construct a BatchId object for the /mail/send API call.
 */
class CVendor_SendGrid_Mail_BatchId implements \JsonSerializable {
    /**
     * @var string This ID represents a batch of emails to be sent at the same time
     */
    private $batch_id;

    /**
     * Optional constructor.
     *
     * @param null|string $batch_id This ID represents a batch of emails to
     *                              be sent at the same time
     */
    public function __construct($batch_id = null) {
        if (isset($batch_id)) {
            $this->setBatchId($batch_id);
        }
    }

    /**
     * Add the batch id to a BatchId object.
     *
     * @param string $batch_id This ID represents a batch of emails to be sent
     *                         at the same time
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setBatchId($batch_id) {
        if (!is_string($batch_id)) {
            throw new CVendor_SendGrid_Exception_TypeException('$batch_id must be of type string.');
        }
        $this->batch_id = $batch_id;
    }

    /**
     * Return the batch id from a BatchId object.
     *
     * @return string
     */
    public function getBatchId() {
        return $this->batch_id;
    }

    /**
     * Return an array representing a BatchId object for the Twilio SendGrid API.
     *
     * @return null|string
     */
    public function jsonSerialize() {
        return $this->getBatchId();
    }
}
