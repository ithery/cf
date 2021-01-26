export default class Url {
    constructor() {

    }
    addQueryString(url, key, value) {
        key = encodeURI(key);
        value = encodeURI(value);
        var urlArray = url.split('?');
        var queryString = '';
        var baseUrl = urlArray[0];
        if (urlArray.length > 1) {
            queryString = urlArray[1];
        }
        var kvp = queryString.split('&');
        var i = kvp.length;
        var x;
        while (i--) {
            x = kvp[i].split('=');
            if (x[0] == key) {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if (i < 0) {
            kvp[kvp.length] = [key, value].join('=');
        }

        queryString = kvp.join('&');
        if (queryString.substr(0, 1) == '&')
            queryString = queryString.substr(1);
        return baseUrl + '?' + queryString;
    };
    replaceParam(url) {
        var available = true;
        while (available) {
            var matches = url.match(/{([\w]*)}/);
            if (matches != null) {
                var key = matches[1];
                var val = null;
                if ($('#' + key).length > 0) {
                    var val = cresenity.value('#' + key);
                }

                if (val == null) {
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