<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CBroadcast_SSE_ServerSentEventStream implements CInterface_Responsable {
    /**
     * @var array<string, string>
     */
    const HEADERS = [
        'Content-Type' => 'text/event-stream',
        'Connection' => 'keep-alive',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, pre-check=0, post-check=0',
        'X-Accel-Buffering' => 'no',
    ];

    protected $channelPrefix = '';

    protected $eventSubscriber;

    protected $responseFactory;

    protected $store;

    protected $eventsHistory;

    public function __construct(
        $eventSubscriber,
        $store,
        $eventsHistory
    ) {
        $this->eventSubscriber = $eventSubscriber;
        $this->responseFactory = CHTTP::responseFactory();
        $this->eventsHistory = $eventsHistory;
        $this->store = $store;
        $this->channelPrefix = CF::config('database.redis.options.prefix', '');
    }

    public function toResponse($request) {
        ini_set('default_socket_timeout', -1);
        set_time_limit(0);

        $socket = CBroadcast::manager()->socket($request);

        return $this->responseFactory->stream(function () use ($request, $socket) {
            (new CBroadcast_SSE_ServerSentEvent('connected', $socket, $request->hasHeader('Last-Event-ID') ? $request->header('Last-Event-ID') : 'wave'))();

            $handler = $this->eventHandler($request, $socket);

            if ($request->hasHeader('Last-Event-ID')) {
                $missedEvents = $this->eventsHistory->getEventsFrom($request->header('Last-Event-ID'), $this->channelPrefix);

                $missedEvents->each(static function ($event) use ($handler) {
                    $handler($event['event'], $event['channel']);
                });
            }

            $this->eventSubscriber->start($handler, $request);
        }, Response::HTTP_OK, self::HEADERS + ['X-Socket-Id' => $socket]);
    }

    protected function eventHandler(CHTTP_Request $request, string $socket) {
        return function ($message, $channel) use ($request, $socket) {
            $channel = $this->removePrefixFromChannel($channel);

            if ($this->needsAuth($channel)) {
                try {
                    $this->authChannel($channel, $request);
                } catch (AccessDeniedHttpException $e) {
                    return;
                }
            }

            list('event' => $event, 'data' => $data) = is_array($message) ? $message : json_decode($message, true, 512, JSON_THROW_ON_ERROR);

            $eventSocketId = carr::pull($data, 'socket');
            $eventId = carr::pull($data, 'broadcast_event_id');

            if ($eventSocketId === $socket) {
                return;
            }

            (new CBroadcast_SSE_ServerSentEvent(
                sprintf('%s.%s', $channel, $event),
                json_encode(['data' => $data], JSON_THROW_ON_ERROR),
                sprintf('%s.%s', $channel, $eventId)
            ))();
        };
    }

    /**
     * @param string        $channel
     * @param CHTTP_Request $request
     *
     * @return void
     */
    protected function authChannel($channel, CHTTP_Request $request) {
        CBroadcast::manager()->auth($request->merge([
            'channel_name' => $channel,
        ]));
    }

    protected function needsAuth(string $channel): bool {
        return cstr::startsWith($channel, 'private-') || cstr::startsWith($channel, 'presence-');
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    protected function removePrefixFromChannel($pattern) {
        return cstr::after($pattern, $this->channelPrefix);
    }
}
