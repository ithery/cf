<?php

class CNotification_TaskQueue_NotificationSender extends CNotification_TaskQueueAbstract {
    /**
     * @var array
     */
    protected array $params;

    /**
     * @param array $params
     */
    public function __construct(array $params) {
        $this->params = $params;
    }

    /**
     * @return void
     */
    public function execute() {
        $channel = carr::get($this->params, 'channel');
        $options = carr::get($this->params, 'options');
        $className = carr::get($this->params, 'className');
        $this->logDaemon('Processing NotificationSender ' . $className . ' with options: ' . json_encode($options));

        try {
            CNotification::manager()->channel($channel)->sendWithoutQueue($className, $options);
        } catch (CModel_Exception_ModelNotFoundException $ex) {
            $this->logDaemon('Ignore Error: ' . $className . ': ' . $ex->getMessage());
        }
        $this->logDaemon('Processed NotificationSender ' . $className);
    }
}
