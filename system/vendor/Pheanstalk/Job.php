<?php

namespace Pheanstalk;

use Pheanstalk\Contract\JobIdInterface;

/**
 * A job in a beanstalkd server.
 */
class Job implements JobIdInterface {

    const STATUS_READY = 'ready';
    const STATUS_RESERVED = 'reserved';
    const STATUS_DELAYED = 'delayed';
    const STATUS_BURIED = 'buried';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $data;

    /**
     * @param int    $id   The job ID
     * @param string $data The job data
     */
    public function __construct($id, $data) {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * The job ID, unique on the beanstalkd server.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * The job data.
     *
     * @return string
     */
    public function getData() {
        return $this->data;
    }

}
