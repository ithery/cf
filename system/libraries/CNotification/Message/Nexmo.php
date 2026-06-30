<?php

class CNotification_Message_Nexmo extends CNotification_MessageAbstract {
    /**
     * @return mixed
     */
    public function send() {
        $nexmo = CVendor::nexmo(carr::get($this->config, 'key'), carr::get($this->config, 'secret'), $this->config);
        $client = $nexmo->getClient();

        return $client->message()->send([
            'type' => $this->getOption('type', 'text'),
            'from' => $this->getOption('from') ?: $nexmo->getFrom(),
            'to' => $this->getOption('recipient'),
            'text' => $this->getOption('message'),
            'client_ref' => $this->getOption('clientReference', ''),
        ]);
    }
}
