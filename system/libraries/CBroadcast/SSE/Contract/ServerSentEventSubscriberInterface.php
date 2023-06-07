<?php

interface CBroadcast_SSE_Contract_ServerSentEventSubscriberInterface {
    public function start(Closure $onMessage, CHTTP_Request $request);
}
