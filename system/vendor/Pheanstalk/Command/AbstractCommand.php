<?php

namespace Pheanstalk\Command;

use Pheanstalk\Contract\CommandInterface;
use Pheanstalk\Contract\ResponseParserInterface;
use Pheanstalk\Exception\CommandException;
use Pheanstalk\Response\ArrayResponse;

/**
 * Common functionality for Command implementations.
 */
abstract class AbstractCommand implements CommandInterface {

    public function hasData() {
        return false;
    }

    public function getData() {
        throw new CommandException('Command has no data');
    }

    public function getDataLength() {
        throw new CommandException('Command has no data');
    }

    public function getResponseParser() {
        if ($this instanceof ResponseParserInterface) {
            return $this;
        }
        throw new \RuntimeException('Concrete implementation must implement `ResponseParser` or override this method');
    }

    /**
     * Creates a Response for the given data.
     */
    protected function createResponse($name, array $data = []) {
        return new ArrayResponse($name, $data);
    }

}
