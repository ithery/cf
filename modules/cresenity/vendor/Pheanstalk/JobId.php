<?php

namespace Pheanstalk;

use Pheanstalk\Contract\JobIdInterface;

class JobId implements JobIdInterface {

    private $id;

    public function __construct($id) {
        if ($id < 0) {
            throw new \InvalidArgumentException('Id must be >= 0');
        }
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

}
