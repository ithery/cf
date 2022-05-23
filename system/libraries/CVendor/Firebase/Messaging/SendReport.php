<?php

class CVendor_Firebase_Messaging_SendReport {
    /**
     * @var CVendor_Firebase_Messaging_MessageTarget
     */
    private $target;

    /**
     * @var null|array
     */
    private $result;

    /**
     * Undocumented variable.
     *
     * @var null|CVendor_Firebase_Messaging_MessageInterface
     */
    private $message = null;

    /**
     * @var null|Throwable
     */
    private $error;

    private function __construct(CVendor_Firebase_Messaging_MessageTarget $target) {
        $this->target = $target;
    }

    public static function success(CVendor_Firebase_Messaging_MessageTarget $target, $response, CVendor_Firebase_Messaging_MessageInterface $message = null) {
        $report = new self($target);
        $report->result = $response;
        $report->message = $message;

        return $report;
    }

    public static function failure(CVendor_Firebase_Messaging_MessageTarget $target, $error, CVendor_Firebase_Messaging_MessageInterface $message = null) {
        $report = new self($target);
        $report->error = $error;
        $report->message = $message;

        return $report;
    }

    /**
     * @return CVendor_Firebase_Messaging_MessageTarget
     */
    public function target() {
        return $this->target;
    }

    public function isSuccess() {
        return $this->error === null;
    }

    public function isFailure() {
        return $this->error !== null;
    }

    /**
     * @return bool
     */
    public function messageTargetWasInvalid() {
        $errorMessage = $this->error !== null ? $this->error->getMessage() : '';

        return $this->messageWasInvalid() && \preg_match('/((not.+valid)|invalid).+token/i', $errorMessage) === 1;
    }

    /**
     * @return bool
     */
    public function messageWasInvalid() {
        return $this->error instanceof CVendor_Firebase_Messaging_Exception_InvalidMessageException;
    }

    /**
     * @return bool
     */
    public function messageWasSentToUnknownToken() {
        return $this->error instanceof CVendor_Firebase_Messaging_Exception_NotFoundException;
    }

    /**
     * @return null|array
     */
    public function result() {
        return $this->result;
    }

    /**
     * @return null|Throwable
     */
    public function error() {
        return $this->error;
    }

    public function message(): ?Message {
        return $this->message;
    }
}
