<?php

namespace Pheanstalk\Command;

/**
 * A command that is executed against a tube
 */
abstract class TubeCommand extends AbstractCommand {

    protected $tube;

    public function __construct($tube) {
        $this->tube = $tube;
    }

}
