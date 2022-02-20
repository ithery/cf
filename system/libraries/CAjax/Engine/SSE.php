<?php

use Symfony\Component\HttpFoundation\StreamedResponse;

class CAjax_Engine_SSE extends CAjax_Engine {
    public function execute() {
        $sseModelClass = CManager_SSE::getModelClass();
        $data = $this->ajaxMethod->getData();
        $interval = carr::get($data, 'interval');
        $clientId = carr::get($data, 'clientId');
        $callback = carr::get($data, 'callback');
        if ($interval == null) {
            $interval = max(CF::config('sse.interval', 5), 1);
        }

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        // delete expired/old
        $this->deleteOld($sseModelClass, $interval, $clientId);

        $response->setCallback(function () use ($sseModelClass, $interval, $clientId) {

            // if the connection has been closed by the client we better exit the loop
            if (connection_aborted()) {
                return;
            }

            $model = $sseModelClass::where('delivered', '=', '0')->where('client', '=', $clientId)->oldest()->first();

            echo ':' . str_repeat(' ', 2048) . "\n"; // 2 kB padding for IE
            echo "retry: 5000\n";

            if (!$model) {
                // no new data to send
                echo ": heartbeat\n\n";
            } else {
                $data = json_encode([
                    'message' => $model->message,
                    'type' => strtolower($model->type),
                    'time' => date('H:i:s A', strtotime($model->created)),
                ]);

                echo 'id: ' . $model->getKey() . "\n";
                echo 'event: ' . $model->event . "\n";
                echo 'data: ' . $data . "\n\n";

                $model->delivered = '1';
                $model->save();
            }

            ob_flush();
            flush();

            sleep($interval);
        });

        return $response->send();
    }

    /**
     * @param string $sseModelClass class name of sse log model
     * @param int    $interval      interval in seconds
     * @param mixed  $clientId
     *
     * @throws \Exception
     */
    public function deleteOld($sseModelClass, $interval, $clientId) {
        $date = new DateTime();
        $date->modify('-' . ($interval * 2) . ' seconds');

        // delete client-specific records
        $sseModelClass::where('created', '<=', $date->format('Y-m-d H:i:s'))
            ->where('clientId', '=', $clientId)
            ->delete();

        // update actual message as delivered
        $sseModelClass::where('created', '<=', $date->format('Y-m-d H:i:s'))
            ->update(['delivered' => '1']);
    }
}
