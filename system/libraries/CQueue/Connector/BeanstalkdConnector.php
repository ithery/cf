<?php

use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;

class CQueue_Connector_BeanstalkdConnector extends CQueue_AbstractConnector {
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return CQueue_AbstractQueue
     */
    public function connect(array $config) {
        return new CQueue_Queue_BeanstalkdQueue(
            $this->pheanstalk($config),
            $config['queue'],
            isset($config['retry_after']) ? $config['retry_after'] : Pheanstalk::DEFAULT_TTR,
            isset($config['block_for']) ? $config['block_for'] : 0
        );
    }

    /**
     * Create a Pheanstalk instance.
     *
     * @param array $config
     *
     * @return \Pheanstalk\Pheanstalk
     */
    protected function pheanstalk(array $config) {
        return Pheanstalk::create(
            $config['host'],
            isset($config['port']) ? $config['port'] : Pheanstalk::DEFAULT_PORT,
            isset($config['timeout']) ? $config['timeout'] : Connection::DEFAULT_CONNECT_TIMEOUT
        );
    }
}
