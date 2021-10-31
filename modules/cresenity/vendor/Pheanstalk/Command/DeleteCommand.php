<?php

namespace Pheanstalk\Command;

use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Contract\ResponseInterface;
use Pheanstalk\Contract\ResponseParserInterface;
use Pheanstalk\Exception;
use Pheanstalk\Response\ArrayResponse;

/**
 * The 'delete' command.
 * Permanently deletes an already-reserved job.
 */
class DeleteCommand extends JobCommand implements ResponseParserInterface {

    public function getCommandLine() {
        return 'delete ' . $this->jobId;
    }

    public function parseResponse($responseLine, $responseData) {
        if ($responseLine == ResponseInterface::RESPONSE_NOT_FOUND) {
            throw new Exception\ServerException(sprintf(
                    'Cannot delete job %u: %s', $this->jobId, $responseLine
            ));
        }

        return $this->createResponse($responseLine);
    }

}
