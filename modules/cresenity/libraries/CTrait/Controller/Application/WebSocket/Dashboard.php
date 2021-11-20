<?php

trait CTrait_Controller_Application_WebSocket_Dashboard {
    public function dashboard() {
        $apps = CWebSocket::appManager();

        $app = c::app();

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
}
