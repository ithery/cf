<?php

namespace Pheanstalk\Command;

use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Contract\ResponseParserInterface;
use Pheanstalk\Job;
use Pheanstalk\YamlResponseParser;

/**
 * The 'stats-job' command.
 *
 * Gives statistical information about the specified job if it exists.
 */
class StatsJobCommand extends JobCommand {

    public function getCommandLine() {
        return sprintf('stats-job %u', $this->jobId);
    }

    public function getResponseParser() {
        return new YamlResponseParser(YamlResponseParser::MODE_DICT);
    }

}
