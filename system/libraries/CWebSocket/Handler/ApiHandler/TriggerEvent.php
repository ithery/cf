<?php

class CWebSocket_Handler_ApiHandler_TriggerEvent extends CWebSocket_Handler_ApiHandlerAbstract {
    /**
     * Handle the incoming request.
     *
     * @return \CHTTP_Response
     */
    public function __invoke() {
        $request = CHTTP::request();
        $channels = $request->channels ?: [];

        if (is_string($channels)) {
            $channels = [$channels];
        }

        foreach ($channels as $channelName) {
            // Here you can use the ->find(), even if the channel
            // does not exist on the server. If it does not exist,
            // then the message simply will get broadcasted
            // across the other servers.
            $channel = $this->channelManager->find(
                $request->appId,
                $channelName
            );
            if ($channel) {
                cdbg::d($channel->getName());
            } else {
                cdbg::d('channel is null');
            }

            $payload = [
                'event' => $request->name,
                'channel' => $channelName,
                'data' => $request->data,
            ];

            if ($channel) {
                $channel->broadcastLocallyToEveryoneExcept(
                    (object) $payload,
                    $request->socket_id,
                    $request->appId
                );
            }

            $this->channelManager->broadcastAcrossServers(
                $request->appId,
                $request->socket_id,
                $channelName,
                (object) $payload
            );

            if ($this->app->statisticsEnabled) {
                CWebSocket::statisticCollector()->apiMessage($request->appId);
            }

            CWebSocket_DashboardLogger::log($request->appId, CWebSocket_DashboardLogger::TYPE_API_MESSAGE, [
                'event' => $request->name,
                'channel' => $channelName,
                'payload' => $request->data,
            ]);
        }

        return (object) [];
    }
}
