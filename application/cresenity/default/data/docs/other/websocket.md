# Module - WebSocket

The `CWebSocket` module provides a real-time WebSocket server built on top of ReactPHP and Ratchet. It uses the Pusher protocol, allowing you to use the Pusher JavaScript client or CF's built-in `CSocket` for client-side connections.

---

### Architecture

```
[Browser]  <--WebSocket-->  [CWebSocket Server (ReactPHP)]  <--Broadcast-->  [PHP App]
              Pusher JS           Port 6001/6002                CBroadcast
```

The WebSocket server runs as a CDaemon service. Your PHP application pushes events via `CBroadcast`, and connected clients receive them in real-time.

---

### Configuration

Create `config/websocket.php` in your application:

```php
<?php
return [
    'enable' => true,
    'dashboard' => [
        'port' => 6002,
        'domain' => c::env('WEBSOCKET_HOST', 'myapp.example.com'),
        'path' => 'cwebsocket',
    ],
    'apps' => [
        'myapp' => [
            'id' => 'myapp',
            'name' => 'myapp',
            'host' => c::env('WEBSOCKET_HOST', 'myapp.example.com'),
            'key' => 'myapp-key',
            'secret' => 'myapp-secret',
            'path' => null,
            'capacity' => null,
            'enable_client_messages' => true,
            'enable_statistics' => false,
            'allowed_origins' => [],
        ],
    ],
    'replication' => [
        'mode' => 'local',
        'modes' => [
            'local' => [
                'channel_manager' => \CWebSocket_ChannelManager_LocalChannelManager::class,
                'collector' => \CWebSocket_Statistic_Collector_MemoryCollector::class,
            ],
        ],
    ],
    'statistics' => [
        'store' => \CWebSocket_Statistic_Store_DatabaseStore::class,
        'interval_in_seconds' => 3600,
        'delete_statistics_older_than_days' => 30,
    ],
    'max_request_size_in_kb' => 250,
    'ssl' => [
        'local_cert' => null,
        'local_pk' => null,
        'passphrase' => null,
        'verify_peer' => false,
        'allow_self_signed' => true,
    ],
    'handlers' => [
        'websocket' => \CWebSocket_Handler_WebSocketHandler::class,
        'health' => \CWebSocket_Handler_HealthHandler::class,
        'trigger_event' => \CWebSocket_Handler_ApiHandler_TriggerEvent::class,
        'fetch_channels' => \CWebSocket_Handler_ApiHandler_FetchChannels::class,
        'fetch_channel' => \CWebSocket_Handler_ApiHandler_FetchChannel::class,
        'fetch_users' => \CWebSocket_Handler_ApiHandler_FetchUsers::class,
    ],
    'promise_resolver' => \React\Promise\FulfilledPromise::class,
];
```

For Redis-based replication (multi-server), change `replication.mode` to `'redis'` and configure the Redis modes section.

---

### Creating the Daemon Service

Extend `CWebSocket_Daemon_WebSocketService`:

```php
<?php
class MyDaemon_Service_WebSocketService extends CWebSocket_Daemon_WebSocketService {
    protected $loopInterval = 60;

    public function setup() {
        c::db()->disableBenchmark();
        $options = [];
        $options['host'] = '0.0.0.0';
        $options['port'] = '6002';
        $options['disableStatistics'] = true;
        $options['debug'] = true;
        $options['loop'] = null;
        $this->websocketOptions = $options;
        $process = new CWebSocket_Process_StartServer($this->websocketOptions, new CDaemon_Output());
        $process->start();
    }

    public function execute() {
    }
}
```

Register in `bootstrap.php`:

```php
CManager::registerDaemon(MyDaemon_Service_WebSocketService::class, null, 'WebSocket');
```

---

### Broadcasting Events

Use `CBroadcast` to push events from server-side PHP to connected WebSocket clients:

```php
// Direct broadcast
CBroadcast::broadcast(['presence-chat.global'], 'CHAT', [
    'message' => 'Hello World',
    'user' => 'System',
    'time' => date('H:i'),
]);
```

Create a broadcast helper class for your application:

```php
<?php
class MyBroadcast {
    public static function broadcast(array $channels, $event = 'BROADCAST', array $payload = []) {
        try {
            CBroadcast::manager()->driver('pusher')->broadcast($channels, $event, $payload);
        } catch (\Exception $ex) {
            // Silently fail if WebSocket is not running
        }
    }

    public static function chatGlobal($message, $username) {
        static::broadcast(['presence-chat.global'], 'CHAT', [
            'message' => $message,
            'username' => $username,
            'time' => date('H:i'),
        ]);
    }

    public static function notifyUser($userId, $title, $message) {
        static::broadcast(['private-user.' . $userId], 'NOTIFICATION', [
            'title' => $title,
            'message' => $message,
        ]);
    }
}
```

---

### Registering Channels

Register broadcast channels in `bootstrap.php` for authorization:

```php
CBroadcast::registerChannel('user', MyBroadcast_UserChannel::class);
CBroadcast::registerChannel('user.{user}', MyBroadcast_UserChannel::class);
CBroadcast::registerChannel('chat.{room}', MyBroadcast_ChatChannel::class);
```

---

### Channel Types

| Prefix | Type | Description |
|---|---|---|
| (none) | Public | Anyone can subscribe |
| `private-` | Private | Requires authentication |
| `presence-` | Presence | Private + member tracking (who is online) |

---

### Client-Side Connection

Use Pusher JS to connect to the WebSocket server:

```html
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
var pusher = new Pusher('myapp-key', {
    wsHost: 'myapp.example.com',
    wsPort: 6002,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws']
});

var channel = pusher.subscribe('presence-chat.global');
channel.bind('CHAT', function(data) {
    console.log(data.message);
});
</script>
```

Or use CF's built-in CSocket (available via `cres.js`):

```javascript
var socket = new CSocket({
    broadcaster: 'pusher',
    key: 'myapp-key',
    wsHost: 'myapp.example.com',
    wsPort: 6002,
    forceTLS: false,
    disableStats: true
});

socket.channel('presence-chat.global')
    .listen('CHAT', function(data) {
        console.log(data.message);
    });
```

---

### Auto-Restart with Cron

Ensure the WebSocket daemon stays running by creating a cron task:

```php
<?php
class MyCron_Cron_Server_RestartPrimaryService extends MyCron_Cron {
    protected $schedule = '*/5 * * * *';

    public function handler() {
        MyTaskQueue_Server_RestartPrimaryService::dispatchNow();
    }
}
```

```php
<?php
class MyTaskQueue_Server_RestartPrimaryService {
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;

    public function execute() {
        $this->restartServiceWhenNotRunning(
            CDaemon::createRunner(MyDaemon_Service_WebSocketService::class)
        );
    }

    private function restartServiceWhenNotRunning(CDaemon_Runner $service) {
        if (!$service->isRunning()) {
            $service->rotateLog();
            $service->start();
            CCron::log('successfully restart ' . $service->getServiceClass());
        }
    }
}
```

Register in `bootstrap.php`:

```php
MyCron::registerCron();
```

---

### Managing from Admin

Add daemon management to your admin panel using the built-in trait:

```php
<?php
class Controller_Admin_Sysadmin_Daemon extends MyAdmin_Controller {
    use CTrait_Controller_Application_Manager_Daemon;

    protected function getTitle() {
        return 'Daemon';
    }
}
```

This provides a UI to start, stop, view logs, and check status of all registered daemons.

---

### Checking WebSocket Status Programmatically

```php
// Check if WebSocket port is reachable
$fp = @fsockopen('127.0.0.1', 6002, $errno, $errstr, 1);
$isRunning = ($fp !== false);
if ($fp) {
    fclose($fp);
}

// Or use CDaemon runner
$runner = CDaemon::createRunner(MyDaemon_Service_WebSocketService::class);
$isRunning = $runner->isRunning();
```
