 /**
 * Global helper object
 */
const helper = {
    DAY: 1000 * 60 * 60 * 24,
    HOUR: 3600,
    defaults: {
        dateSettings: {
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            months: [
                'January', 'February', 'March', 'April', 'May', 'June', 'July',
                'August', 'September', 'October', 'November', 'December'
            ],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            meridiem: ['AM', 'PM'],
            ordinal: function (number) {
                var n = number % 10, suffixes = {1: 'st', 2: 'nd', 3: 'rd'};
                return Math.floor(number % 100 / 10) === 1 || !suffixes[n] ? 'th' : suffixes[n];
            }
        },
        separators: /[ \-+\/.:@]/g,
        validParts: /[dDjlNSwzWFmMntLoYyaABgGhHisueTIOPZcrU]/g,
        intParts: /[djwNzmnyYhHgGis]/g,
        tzParts: /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
        tzClip: /[^-+\dA-Z]/g
    },
    getInt: function (str, radix) {
        return parseInt(str, (radix ? radix : 10));
    },
    compare: function (str1, str2) {
        return typeof (str1) === 'string' && typeof (str2) === 'string' && str1.toLowerCase() === str2.toLowerCase();
    },
    lpad: function (value, length, chr) {
        var val = value.toString();
        chr = chr || '0';
        return val.length < length ? helper.lpad(chr + val, length) : val;
    },
    merge: function (out) {
        var i, obj;
        out = out || {};
        for (i = 1; i < arguments.length; i++) {
            obj = arguments[i];
            if (!obj) {
                continue;
            }
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    if (typeof obj[key] === 'object') {
                        $h.merge(out[key], obj[key]);
                    } else {
                        out[key] = obj[key];
                    }
                }
            }
        }
        return out;
    },
    getIndex: function (val, arr) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i].toLowerCase() === val.toLowerCase()) {
                return i;
            }
        }
        return -1;
    }
};

export default helper;
