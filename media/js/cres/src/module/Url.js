export default class Url {
    constructor() {

    }
    addQueryString(url, key, value) {
        key = encodeURI(key);
        value = encodeURI(value);
        let urlArray = url.split('?');
        let queryString = '';
        let baseUrl = urlArray[0];
        if (urlArray.length > 1) {
            queryString = urlArray[1];
        }
        let kvp = queryString.split('&');
        let i = kvp.length;
        let x;
        while (i--) {
            x = kvp[i].split('=');
            if (x[0] === key) {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if (i < 0) {
            kvp[kvp.length] = [key, value].join('=');
        }

        queryString = kvp.join('&');
        if (queryString.substr(0, 1) === '&') {queryString = queryString.substr(1);}
        return baseUrl + '?' + queryString;
    }
    replaceParam(url) {
        let available = true;
        while (available) {
            let matches = url.match(/{([\w]*)}/);
            if (matches !== null) {
                let key = matches[1];
                let val = null;
                if ($('#' + key).length > 0) {
                    val = window.cresenity.value('#' + key);
                }

                if (val === null) {
                    val = key;
                }

                url = url.replace('{' + key + '}', val);
            } else {
                available = false;
            }
        }
        return url;
    }
}
