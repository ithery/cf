@if(c::app()->notification()->getDriver()=='firebase')
<script src="https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js"></script>
@endif
<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ c::app()->notification()->serviceWorkerUrl() }}', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            @if(c::app()->notification()->isDebug())
            console.log('CF Notification: ServiceWorker registration successful with scope: ', registration.scope);
            @endif
        }, function (err) {
            // registration failed :(
            @if(c::app()->notification()->isDebug())
            console.log('CF Notification: ServiceWorker registration failed: ', err);
            @endif
        });
    }
</script>
@if(c::app()->notification()->getDriver()=='firebase')
<script type="text/javascript">
    firebase.initializeApp(@json(c::app()->notification()->getOptions()));
    const messaging = firebase.messaging();

    messaging.requestPermission()
    .then(function() {
        return messaging.getToken();
    })
    .then((currentToken) => {
        console.log(cu)
        if (currentToken) {
            sendTokenToServer(currentToken);
        } else {
            // Show permission request.
            console.log('No registration token available. Request permission to generate one.');
            // Show permission UI.
            setTokenSentToServer(false);


        }
    }).catch((err) => {
        setTokenSentToServer(false);
    });


    // Send the registration token your application server, so that it can:
    // - send messages back to this app
    // - subscribe/unsubscribe the token from topics
    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer()) {
            console.log('Sending token to server...');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ c::app()->notification()->getSendTokenUrl() }}' + currentToken, true)
            xhr.onload = () => {
                if (this.status == 200) {
                    console.log('Request succeeded with JSON response', data);

                    setTokenSentToServer(currentToken);
                }
            }
            xhr.send()

        } else {
            console.log('Token already sent to server so won\'t send it again ' +
                'unless it changes');
        }
    }

    function isTokenSentToServer() {
        const token = localStorage.getItem('{{ c::app()->notification()->getTokenLocalStorageKey() }}');
        console.log(token);
        if (token == '0') {
            return false;
        }
        return true;
    }

    function setTokenSentToServer(sent) {
        localStorage.setItem('{{ c::app()->notification()->getTokenLocalStorageKey() }}', sent === false ? '0' : send);
    }

</script>
@endif
