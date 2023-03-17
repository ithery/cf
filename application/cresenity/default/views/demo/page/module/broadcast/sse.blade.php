<div id="sse-event">
</div>

@CAppPushScript
<script>
    const tweets = [];
    window.addEventListener('cresenity:loaded', function() {
        if (typeof window.csocket == 'undefined') {
            window.csocket = cresenity.createWebSocket({
                broadcaster: 'sse',
            });

            window.csocket.connector.connection.addEventListener('connected', () => {
                console.log('connected with SSE');
            });

            window.csocket.channel("event-stream").listen("NewEvent", (e) =>
                tweets.value.unshift(e)
            );

        }

    });
</script>
@CAppEndPushScript
