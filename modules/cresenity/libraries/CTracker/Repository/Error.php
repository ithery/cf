<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:39:33 PM
 */
class CTracker_Repository_Error extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('errorModel', CTracker_Model_Error::class);
        $this->createModel();

        parent::__construct();
    }

    public function getMessageFromException($exception) {
        if ($message = $exception->getMessage()) {
            return $message;
        }

        return $message;
    }

    public function getCodeFromException($exception) {
        if (method_exists($exception, 'getCode') && $code = $exception->getCode()) {
            return $code;
        }
        if (method_exists($exception, 'getStatusCode') && $code = $exception->getStatusCode()) {
            return $code;
        }
    }
}
