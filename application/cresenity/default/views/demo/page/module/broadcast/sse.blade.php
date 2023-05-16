<div>
    <a href="#" class="btn btn-primary" id="btn-send">Send</a>
</div>
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

            // window.csocket.connector.connection.addEventListener('connected', () => {
            //     console.log('connected with SSE');
            // });
            window.csocket.channel("event-stream").listen("NewEvent", (e) => {
                cresenity.toast('success',e.message);
            });

        }

    });
    const btnSend = document.getElementById('btn-send');
    btnSend.addEventListener('click', async () => {
        let response = await fetch('/demo/module/broadcast/sse/send');
        let json = await response.json();
        console.log(json);
    });
</script>
@CAppEndPushScript
