<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

class CWebSocket_Handler_ApiHandler_FetchChannels extends CWebSocket_Handler_ApiHandlerAbstract {
    /**
     * Handle the incoming request.
     *
     * @return \CHTTP_Response
     */
    public function __invoke() {
        $request = CHTTP::request();
        $attributes = [];

        if ($request->has('info')) {
            $attributes = explode(',', trim($request->info));

            if (in_array('user_count', $attributes) && !cstr::startsWith($request->filter_by_prefix, 'presence-')) {
                throw new HttpException(400, 'Request must be limited to presence channels in order to fetch user_count');
            }
        }

        return $this->channelManager
            ->getGlobalChannels($request->appId)
            ->then(function ($channels) use ($request, $attributes) {
                $channels = c::collect($channels)->keyBy(function ($channel) {
                    return $channel instanceof CWebSocket_Channel
                        ? $channel->getName()
                        : $channel;
                });

                if ($request->has('filter_by_prefix')) {
                    $channels = $channels->filter(function ($channel, $channelName) use ($request) {
                        return cstr::startsWith($channelName, $request->filter_by_prefix);
                    });
                }

                $channelNames = $channels->map(function ($channel) {
                    return $channel instanceof CWebSocket_Channel
                        ? $channel->getName()
                        : $channel;
                })->toArray();

                return $this->channelManager
                    ->getChannelsMembersCount($request->appId, $channelNames)
                    ->then(function ($counts) use ($channels, $attributes) {
                        $channels = $channels->map(function ($channel) use ($counts, $attributes) {
                            $info = new stdClass();

                            $channelName = $channel instanceof CWebSocket_Channel
                                ? $channel->getName()
                                : $channel;

                            if (in_array('user_count', $attributes)) {
                                $info->user_count = $counts[$channelName];
                            }

                            return $info;
                        })->sortBy(function ($content, $name) {
                            return $name;
                        })->all();

                        return [
                            'channels' => $channels ?: new stdClass(),
                        ];
                    });
            });
    }
}
