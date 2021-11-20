<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

class CWebSocket_Handler_ApiHandler_FetchUsers extends CWebSocket_Handler_ApiHandlerAbstract {
    /**
     * Handle the incoming request.
     *
     * @return \CHTTP_Response
     */
    public function __invoke() {
        $request = CHTTP::request();
        if (!cstr::startsWith($request->channelName, 'presence-')) {
            return new HttpException(400, "Invalid presence channel `{$request->channelName}`");
        }

        return $this->channelManager
            ->getChannelMembers($request->appId, $request->channelName)
            ->then(function ($members) {
                $users = c::collect($members)->map(function ($user) {
                    return ['id' => $user->user_id];
                })->values()->toArray();

                return [
                    'users' => $users,
                ];
            });
    }
}
