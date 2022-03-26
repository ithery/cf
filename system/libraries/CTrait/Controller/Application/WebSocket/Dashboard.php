<?php

trait CTrait_Controller_Application_WebSocket_Dashboard {
    use CWebSocket_Concern_PushesToPusherTrait;

    public function dashboard($method = null, ...$args) {
        if ($method == 'auth') {
            return $this->dashboardAuth();
        }
        if ($method == 'statistic') {
            return $this->dashboardStatistic(...$args);
        }
        if ($method == 'event') {
            return $this->dashboardEvent();
        }
        $apps = CWebSocket::appManager();

        $app = c::app();
        $app->title('WebSocket Dashboard');

        c::manager()->registerCss('https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css');
        c::manager()->registerCss('https://cdn.jsdelivr.net/npm/vue-json-editor@1.4.2/assets/jsoneditor.min.css');

        c::manager()->registerJs('https://js.pusher.com/7.0/pusher.min.js', CClientScript::POS_HEAD);
        c::manager()->registerJs('https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.min.js', CClientScript::POS_HEAD);
        c::manager()->registerJs('https://cdn.plot.ly/plotly-latest.min.js', CClientScript::POS_HEAD);
        c::manager()->registerJs('https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', CClientScript::POS_HEAD);
        c::manager()->registerJs('https://cdn.jsdelivr.net/npm/v-jsoneditor@1.4.1/dist/v-jsoneditor.min.js', CClientScript::POS_HEAD);
        $app->addView('cresenity.websocket.dashboard', [
            'apps' => $apps->all(),
            'port' => CF::config('websocket.dashboard.port', 6001),
            'channels' => CWebSocket_DashboardLogger::$channels,
            'logPrefix' => CWebSocket_DashboardLogger::LOG_CHANNEL_PREFIX,
            'refreshInterval' => CF::config('websocket.statistics.interval_in_seconds'),
        ]);

        return $app;
    }

    protected function dashboardAuth() {
        $request = c::request();
        $app = CWebSocket_App::findById($request->header('X-App-Id'));
        $broadcaster = $this->getPusherBroadcaster([
            'key' => $app->key,
            'secret' => $app->secret,
            'id' => $app->id,
        ]);

        /*
         * Since the dashboard itself is already secured by the
         * Authorize middleware, we can trust all channel
         * authentication requests in here.
         */
        return $broadcaster->validAuthenticationResponse($request, []);
    }

    protected function dashboardStatistic($appId) {
        $processQuery = function ($query) use ($appId) {
            return $query->where('app', '=', $appId)
                ->latest()
                ->limit(120);
        };

        $processCollection = function ($collection) {
            return $collection->reverse();
        };

        return CWebSocket::statisticStore()->getForGraph(
            $processQuery,
            $processCollection
        );
    }

    protected function dashboardEvent() {
        $request = c::request();
        $request->validate([
            'appId' => ['required', new CWebSocket_Rule_AppId()],
            'key' => 'required|string',
            'secret' => 'required|string',
            'event' => 'required|string',
            'channel' => 'required|string',
            'data' => 'required|json',
        ]);

        $broadcaster = $this->getPusherBroadcaster([
            'key' => $request->key,
            'secret' => $request->secret,
            'id' => $request->appId,
        ]);

        try {
            $decodedData = json_decode($request->data, true);

            $broadcaster->broadcast(
                [$request->channel],
                $request->event,
                $decodedData ?: []
            );
        } catch (Exception $e) {
            return c::response()->json([
                'ok' => false,
                'exception' => $e->getMessage(),
            ]);
        }

        return c::response()->json([
            'ok' => true,
        ]);
    }
}
