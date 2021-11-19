<?php
use Symfony\Component\HttpKernel\Exception\HttpException;

class CWebSocket_Handler_ApiHandler_FetchChannel extends CWebSocket_Handler_ApiHandlerAbstract {
    /**
     * Handle the incoming request.
     *
     * @return \CHTTP_Response
     */
    public function __invoke() {
        $request = CHTTP::request();
        $channel = $this->channelManager->find(
            $request->appId,
            $request->channelName
        );

        if (is_null($channel)) {
            return new HttpException(404, "Unknown channel `{$request->channelName}`.");
        }

        return $this->channelManager
            ->getGlobalConnectionsCount($request->appId, $request->channelName)
            ->then(function ($connectionsCount) use ($request) {
                // For the presence channels, we need a slightly different response
                // that need an additional call.
                if (cstr::startsWith($request->channelName, 'presence-')) {
                    return $this->channelManager
                        ->getChannelsMembersCount($request->appId, [$request->channelName])
                        ->then(function ($channelMembers) use ($connectionsCount, $request) {
                            return [
                                'occupied' => $connectionsCount > 0,
                                'subscription_count' => $connectionsCount,
                                'user_count' => $channelMembers[$request->channelName] ?? 0,
                            ];
                        });
                }

                // For the rest of the channels, we might as well
                // send the basic response with the subscriptions count.
                return [
                    'occupied' => $connectionsCount > 0,
                    'subscription_count' => $connectionsCount,
                ];
            });
    }
}
