@if (c::app()->notification()->getDriver() == 'firebase')
    <script src="https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js"></script>

    <script type="text/javascript">
        firebase.initializeApp(@json(c::app()->notification()->getOptions()));
        const messaging = firebase.messaging();

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ c::app()->notification()->serviceWorkerUrl() }}', {
                scope: '{{ c::app()->notification()->serviceWorkerScope() }}'
            }).then(function(registration) {

                messaging.onMessage((payload) => {
                    console.log('Message received. ', payload);
                    if (payload && payload.notification) {
                        cresenity.toast('info', payload.notification.title + '<br/>' + payload.notification.body);
                    }
                    cresenity.dispatch('notification:message', {payload : payload});
                });

                // Send the registration token your application server, so that it can:
                // - send messages back to this app
                // - subscribe/unsubscribe the token from topics
                function sendTokenToServer(currentToken) {
                    if (!isTokenSentToServer(currentToken)) {
                        console.log('Sending token to server...');
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ c::app()->notification()->getSendTokenUrl() }}' + '/' + currentToken,
                            true)
                        xhr.onload = function() {
                            if (this.readyState == 4 && this.status == 200) {

                                console.log('Request succeeded with response', this.responseText);
                                const body = JSON.parse(this.responseText);
                                if(body && body.errCode==0) {
                                    setTokenSentToServer(currentToken);
                                    console.log('Token Saved');
                                }

                            }
                        }
                        xhr.send()

                    } else {
                        console.log('Token already sent to server so won\'t send it again ' +
                            'unless it changes');
                    }
                }

                function isTokenSentToServer(currentToken) {
                    const token = localStorage.getItem('{{ c::app()->notification()->getTokenLocalStorageKey() }}');

                    if (token == '0' || token == null || token == false) {
                        return false;
                    }
                    if(currentToken == undefined) {
                        return true;
                    }
                    return token!=currentToken;
                }

                function setTokenSentToServer(sent) {
                    localStorage.setItem('{{ c::app()->notification()->getTokenLocalStorageKey() }}', sent ===
                        false ? '0' :
                        sent);
                }

                function requestPermission() {
                    console.log('Requesting permission...');
                    Notification.requestPermission().then((permission) => {
                        console.log(permission)
                        if (permission === 'granted') {
                            messaging.getToken({
                                serviceWorkerRegistration: registration
                            }).then((currentToken) => {
                                console.log('get Token:' + currentToken)
                                if (currentToken) {
                                    sendTokenToServer(currentToken);
                                } else {
                                    // Show permission request.
                                    console.log(
                                        'No registration token available. Request permission to generate one.'
                                    );
                                    // Show permission UI.
                                    setTokenSentToServer(false);


                                }
                            }).catch((err) => {
                                console.log('Error when get token');
                                console.error(err);
                                setTokenSentToServer(false);
                            });

                        } else {
                            setTokenSentToServer(false);
                        }
                    });
                }
                if (!isTokenSentToServer()) {
                    requestPermission();
                }

            }, function(err) {
                // registration failed :(
                @if (c::app()->notification()->isDebug())
                    console.log('CF Notification: ServiceWorker registration failed: ', err);
                @endif

            });
        }
    </script>
@endif
