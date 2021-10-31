
class LiveReload {
    construtor() {
        if ('WebSocket' in window) {
            let protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
            let address = protocol + window.location.host + window.location.pathname + '/ws';
            let socket = new WebSocket(address);
            socket.onmessage = function (msg) {
                if (msg.data == 'reload') {
                    window.location.reload();
                } else if (msg.data == 'refreshcss') {
                    this.refreshCSS();
                }
            };
            if (sessionStorage && !sessionStorage.getItem('cresenity.livereload.enable')) {
                console.log('Live reload enabled.');
                sessionStorage.setItem('cresenity.livereload.enable', true);
            }
        } else {
            console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
        }
    }
    refreshCSS() {
        let sheets = [].slice.call(document.getElementsByTagName('link'));
        let head = document.getElementsByTagName('head')[0];
        for (let i = 0; i < sheets.length; ++i) {
            let elem = sheets[i];
            let parent = elem.parentElement || head;
            parent.removeChild(elem);
            let rel = elem.rel;
            if (elem.href && typeof rel != 'string' || rel.length == 0 || rel.toLowerCase() == 'stylesheet') {
                let url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
                elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
            }
            parent.appendChild(elem);
        }
    }
}
