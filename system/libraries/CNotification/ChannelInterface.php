<?php

interface CNotification_ChannelInterface {
    /**
     * @param string $className
     * @param array  $options
     * @param array  $config
     *
     * @return mixed
     */
    public function send($className, array $options = [], array $config = []);

    /**
     * @param string $className
     * @param array  $options
     *
     * @return mixed
     */
    public function sendWithoutQueue($className, array $options = []);
}
