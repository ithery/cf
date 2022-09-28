<?php

use Symfony\Component\HttpFoundation\StreamedResponse;

class CAjax_Engine_ProgressBarProcess extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();
        $callable = carr::get($data, 'callable');
        $config = carr::get($data, 'config');
        if ($callable) {
            $callable = unserialize($callable);
        }
        //create the processHandler
        $response = new StreamedResponse();
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->setCallback(function () use ($callable, $config) {
            ob_implicit_flush(true);
            $out = '';
            while (ob_get_level()) {
                echo ob_get_level() . "\n";
                $out .= ob_get_clean();
            }

            $processHandler = new CElement_Component_ProgressBar_ProcessHandler($callable, $config);

            $processHandler->setNotifyListener(function ($script) {
                // if the connection has been closed by the client we better exit the loop

                if (connection_aborted()) {
                    return;
                }
                echo $script;
                flush();
            });
            $processHandler->startProcess();
        });

        return $response->send();
    }
}
