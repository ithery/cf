<?php

class CQueue_Connector_FileConnector extends CQueue_AbstractConnector {
    /**
     * Queue connection.
     *
     * @param array $config
     *
     * @return \CQueue_Queue_FileQueue
     */
    public function connect(array $config) {
        if (!isset($config['queue'])) {
            $config['queue']='default';
        }
        if (!isset($config['path'])) {
            $config['path']=DOCROOT.'temp'.DS.'queue'.CF::appCode().DS.$config['queue'];
        }

        return new CQueue_Queue_FileQueue(
            $config['path'],
            $config['queue']
        );
    }
}
