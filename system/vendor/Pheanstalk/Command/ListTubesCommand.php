<?php

namespace Pheanstalk\Command;

use Pheanstalk\Contract\ResponseParserInterface;
use Pheanstalk\YamlResponseParser;

/**
 * The 'list-tubes' command.
 *
 * List all existing tubes.
 */
class ListTubesCommand extends AbstractCommand {

    public function getCommandLine() {
        return 'list-tubes';
    }

    public function getResponseParser() {
        return new YamlResponseParser(YamlResponseParser::MODE_LIST);
    }

}
