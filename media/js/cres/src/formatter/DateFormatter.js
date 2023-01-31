import helper from './utils/date-formatter-helper';

class DateFormatter {
    constructor(options) {
        const config = helper.merge(helper.defaults, options);
        this.dateSettings = config.dateSettings;
        this.separators = config.separators;
        this.validParts = config.validParts;
        this.intParts = config.intParts;
        this.tzParts = config.tzParts;
        this.tzClip = config.tzClip;
    }
    getMonth(val) {
        let i = helper.getIndex(val, this.dateSettings.monthsShort) + 1;
        if (i === 0) {
            i = helper.getIndex(val, this.dateSettings.months) + 1;
        }
        return i;
    }
    parseDate(vDate, vFormat) {
        const vSettings = this.dateSettings;
        let vDateFlag = false;
        let vTimeFlag = false;
        let len, mer;


        const out = {date: null, year: null, month: null, day: null, hour: 0, min: 0, sec: 0};
        if (!vDate) {
            return null;
        }
        if (vDate instanceof Date) {
            return vDate;
        }
        if (vFormat === 'U') {
            let i = helper.getInt(vDate);
            return i ? new Date(i * 1000) : vDate;
        }
        switch (typeof vDate) {
            case 'number':
                return new Date(vDate);
            case 'string':
                break;
            default:
                return null;
        }
        let vFormatParts = vFormat.match(this.validParts);
        if (!vFormatParts || vFormatParts.length === 0) {
            throw new Error('Invalid date format definition.');
        }
        for (let i = vFormatParts.length - 1; i >= 0; i--) {
            if (vFormatParts[i] === 'S') {
                vFormatParts.splice(i, 1);
            }
        }
        const vDateParts = vDate.replace(this.separators, '\0').split('\0');
        for (let i = 0; i < vDateParts.length; i++) {
            const vDatePart = vDateParts[i];
            const iDatePart = helper.getInt(vDatePart);
            switch (vFormatParts[i]) {
                case 'y':
                case 'Y':
                    if (iDatePart) {
                        len = vDatePart.length;
                        out.year = len === 2 ? helper.getInt((iDatePart < 70 ? '20' : '19') + vDatePart) : iDatePart;
                    } else {
                        return null;
                    }
                    vDateFlag = true;
                    break;
                case 'm':
                case 'n':
                case 'M':
                case 'F':
                    if (isNaN(iDatePart)) {
                        const vMonth = this.getMonth(vDatePart);
                        if (vMonth > 0) {
                            out.month = vMonth;
                        } else {
                            return null;
                        }
                    } else {
                        if (iDatePart >= 1 && iDatePart <= 12) {
                            out.month = iDatePart;
                        } else {
                            return null;
                        }
                    }
                    vDateFlag = true;
                    break;
                case 'd':
                case 'j':
                    if (iDatePart >= 1 && iDatePart <= 31) {
                        out.day = iDatePart;
                    } else {
                        return null;
                    }
                    vDateFlag = true;
                    break;
                case 'g':
                case 'h':
                    // eslint-disable-next-line no-case-declarations
                    const vMeriIndex = (vFormatParts.indexOf('a') > -1) ? vFormatParts.indexOf('a') :
                        ((vFormatParts.indexOf('A') > -1) ? vFormatParts.indexOf('A') : -1);
                    mer = vDateParts[vMeriIndex];
                    if (vMeriIndex !== -1) {
                        const vMeriOffset = helper.compare(mer, vSettings.meridiem[0]) ? 0 :
                            (helper.compare(mer, vSettings.meridiem[1]) ? 12 : -1);
                        if (iDatePart >= 1 && iDatePart <= 12 && vMeriOffset !== -1) {
                            out.hour = iDatePart % 12 === 0 ? vMeriOffset : iDatePart + vMeriOffset;
                        } else {
                            if (iDatePart >= 0 && iDatePart <= 23) {
                                out.hour = iDatePart;
                            }
                        }
                    } else {
                        if (iDatePart >= 0 && iDatePart <= 23) {
                            out.hour = iDatePart;
                        } else {
                            return null;
                        }
                    }
                    vTimeFlag = true;
                    break;
                case 'G':
                case 'H':
                    if (iDatePart >= 0 && iDatePart <= 23) {
                        out.hour = iDatePart;
                    } else {
                        return null;
                    }
                    vTimeFlag = true;
                    break;
                case 'i':
                    if (iDatePart >= 0 && iDatePart <= 59) {
                        out.min = iDatePart;
                    } else {
                        return null;
                    }
                    vTimeFlag = true;
                    break;
                case 's':
                    if (iDatePart >= 0 && iDatePart <= 59) {
                        out.sec = iDatePart;
                    } else {
                        return null;
                    }
                    vTimeFlag = true;
                    break;
                default:
                    break;
            }
        }
        if (vDateFlag === true) {
            let varY = out.year || 0, varM = out.month ? out.month - 1 : 0, varD = out.day || 1;
            out.date = new Date(varY, varM, varD, out.hour, out.min, out.sec, 0);
        } else {
            if (vTimeFlag !== true) {
                return null;
            }
            out.date = new Date(0, 0, 0, out.hour, out.min, out.sec, 0);
        }
        return out.date;
    }
    guessDate(vDateStr, vFormat) {
        if (typeof vDateStr !== 'string') {
            return vDateStr;
        }
        const vParts = vDateStr.replace(this.separators, '\0').split('\0');
        const vPattern = /^[djmn]/g;
        const vFormatParts = vFormat.match(this.validParts);
        let vDate = new Date();
        let vDigit = 0;
        let len, vYear, i, n, iPart, iSec;

        if (!vPattern.test(vFormatParts[0])) {
            return vDateStr;
        }

        for (i = 0; i < vParts.length; i++) {
            vDigit = 2;
            iPart = vParts[i];
            iSec = helper.getInt(iPart.substr(0, 2));
            if (isNaN(iSec)) {
                return null;
            }
            switch (i) {
                case 0:
                    if (vFormatParts[0] === 'm' || vFormatParts[0] === 'n') {
                        vDate.setMonth(iSec - 1);
                    } else {
                        vDate.setDate(iSec);
                    }
                    break;
                case 1:
                    if (vFormatParts[0] === 'm' || vFormatParts[0] === 'n') {
                        vDate.setDate(iSec);
                    } else {
                        vDate.setMonth(iSec - 1);
                    }
                    break;
                case 2:
                    vYear = vDate.getFullYear();
                    len = iPart.length;
                    vDigit = len < 4 ? len : 4;
                    vYear = helper.getInt(len < 4 ? vYear.toString().substr(0, 4 - len) + iPart : iPart.substr(0, 4));
                    if (!vYear) {
                        return null;
                    }
                    vDate.setFullYear(vYear);
                    break;
                case 3:
                    vDate.setHours(iSec);
                    break;
                case 4:
                    vDate.setMinutes(iSec);
                    break;
                case 5:
                    vDate.setSeconds(iSec);
                    break;
                default:
                    break;
            }
            n = iPart.substr(vDigit);
            if (n.length > 0) {
                vParts.splice(i + 1, 0, n);
            }
        }
        return vDate;
    }
    parseFormat(vChar, vDate) {
        const vSettings = this.dateSettings;
        let fmt;
        const backslash = /\\?(.?)/gi;
        const doFormat = function (t, s) {
            return fmt[t] ? fmt[t]() : s;
        };
        fmt = {
            /////////
            // DAY //
            /////////
            /**
             * Day of month with leading 0: `01..31`
             * @return {string}
             */
            d: function () {
                return helper.lpad(fmt.j(), 2);
            },
            /**
             * Shorthand day name: `Mon...Sun`
             * @return {string}
             */
            D: function () {
                return vSettings.daysShort[fmt.w()];
            },
            /**
             * Day of month: `1..31`
             * @return {number}
             */
            j: function () {
                return vDate.getDate();
            },
            /**
             * Full day name: `Monday...Sunday`
             * @return {string}
             */
            l: function () {
                return vSettings.days[fmt.w()];
            },
            /**
             * ISO-8601 day of week: `1[Mon]..7[Sun]`
             * @return {number}
             */
            N: function () {
                return fmt.w() || 7;
            },
            /**
             * Day of week: `0[Sun]..6[Sat]`
             * @return {number}
             */
            w: function () {
                return vDate.getDay();
            },
            /**
             * Day of year: `0..365`
             * @return {number}
             */
            z: function () {
                let a = new Date(fmt.Y(), fmt.n() - 1, fmt.j()), b = new Date(fmt.Y(), 0, 1);
                return Math.round((a - b) / helper.DAY);
            },

            //////////
            // WEEK //
            //////////
            /**
             * ISO-8601 week number
             * @return {number}
             */
            W: function () {
                let a = new Date(fmt.Y(), fmt.n() - 1, fmt.j() - fmt.N() + 3), b = new Date(a.getFullYear(), 0, 4);
                return helper.lpad(1 + Math.round((a - b) / helper.DAY / 7), 2);
            },

            ///////////
            // MONTH //
            ///////////
            /**
             * Full month name: `January...December`
             * @return {string}
             */
            F: function () {
                return vSettings.months[vDate.getMonth()];
            },
            /**
             * Month w/leading 0: `01..12`
             * @return {string}
             */
            m: function () {
                return helper.lpad(fmt.n(), 2);
            },
            /**
             * Shorthand month name; `Jan...Dec`
             * @return {string}
             */
            M: function () {
                return vSettings.monthsShort[vDate.getMonth()];
            },
            /**
             * Month: `1...12`
             * @return {number}
             */
            n: function () {
                return vDate.getMonth() + 1;
            },
            /**
             * Days in month: `28...31`
             * @return {number}
             */
            t: function () {
                return (new Date(fmt.Y(), fmt.n(), 0)).getDate();
            },

            //////////
            // YEAR //
            //////////
            /**
             * Is leap year? `0 or 1`
             * @return {number}
             */
            L: function () {
                let Y = fmt.Y();
                return (Y % 4 === 0 && Y % 100 !== 0 || Y % 400 === 0) ? 1 : 0;
            },
            /**
             * ISO-8601 year
             * @return {number}
             */
            o: function () {
                let n = fmt.n(), W = fmt.W(), Y = fmt.Y();
                return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
            },
            /**
             * Full year: `e.g. 1980...2010`
             * @return {number}
             */
            Y: function () {
                return vDate.getFullYear();
            },
            /**
             * Last two digits of year: `00...99`
             * @return {string}
             */
            y: function () {
                return fmt.Y().toString().slice(-2);
            },

            //////////
            // TIME //
            //////////
            /**
             * Meridian lower: `am or pm`
             * @return {string}
             */
            a: function () {
                return fmt.A().toLowerCase();
            },
            /**
             * Meridian upper: `AM or PM`
             * @return {string}
             */
            A: function () {
                let n = fmt.G() < 12 ? 0 : 1;
                return vSettings.meridiem[n];
            },
            /**
             * Swatch Internet time: `000..999`
             * @return {string}
             */
            B: function () {
                let H = vDate.getUTCHours() * helper.HOUR, i = vDate.getUTCMinutes() * 60, s = vDate.getUTCSeconds();
                return helper.lpad(Math.floor((H + i + s + helper.HOUR) / 86.4) % 1000, 3);
            },
            /**
             * 12-Hours: `1..12`
             * @return {number}
             */
            g: function () {
                return fmt.G() % 12 || 12;
            },
            /**
             * 24-Hours: `0..23`
             * @return {number}
             */
            G: function () {
                return vDate.getHours();
            },
            /**
             * 12-Hours with leading 0: `01..12`
             * @return {string}
             */
            h: function () {
                return helper.lpad(fmt.g(), 2);
            },
            /**
             * 24-Hours w/leading 0: `00..23`
             * @return {string}
             */
            H: function () {
                return helper.lpad(fmt.G(), 2);
            },
            /**
             * Minutes w/leading 0: `00..59`
             * @return {string}
             */
            i: function () {
                return helper.lpad(vDate.getMinutes(), 2);
            },
            /**
             * Seconds w/leading 0: `00..59`
             * @return {string}
             */
            s: function () {
                return helper.lpad(vDate.getSeconds(), 2);
            },
            /**
             * Microseconds: `000000-999000`
             * @return {string}
             */
            u: function () {
                return helper.lpad(vDate.getMilliseconds() * 1000, 6);
            },

            //////////////
            // TIMEZONE //
            //////////////
            /**
             * Timezone identifier: `e.g. Atlantic/Azores, ...`
             * @return {string}
             */
            e: function () {
                let str = /\((.*)\)/.exec(String(vDate))[1];
                return str || 'Coordinated Universal Time';
            },
            /**
             * DST observed? `0 or 1`
             * @return {number}
             */
            I: function () {
                let a = new Date(fmt.Y(), 0), c = Date.UTC(fmt.Y(), 0),
                    b = new Date(fmt.Y(), 6), d = Date.UTC(fmt.Y(), 6);
                return ((a - c) !== (b - d)) ? 1 : 0;
            },
            /**
             * Difference to GMT in hour format: `e.g. +0200`
             * @return {string}
             */
            O: function () {
                let tzo = vDate.getTimezoneOffset(), a = Math.abs(tzo);
                return (tzo > 0 ? '-' : '+') + helper.lpad(Math.floor(a / 60) * 100 + a % 60, 4);
            },
            /**
             * Difference to GMT with colon: `e.g. +02:00`
             * @return {string}
             */
            P: function () {
                let O = fmt.O();
                return (O.substr(0, 3) + ':' + O.substr(3, 2));
            },
            /**
             * Timezone abbreviation: `e.g. EST, MDT, ...`
             * @return {string}
             */
            T: function () {
                let str = (String(vDate).match(this.tzParts) || ['']).pop().replace(this.tzClip, '');
                return str || 'UTC';
            },
            /**
             * Timezone offset in seconds: `-43200...50400`
             * @return {number}
             */
            Z: function () {
                return -vDate.getTimezoneOffset() * 60;
            },

            ////////////////////
            // FULL DATE TIME //
            ////////////////////
            /**
             * ISO-8601 date
             * @return {string}
             */
            c: function () {
                return 'Y-m-d\\TH:i:sP'.replace(backslash, doFormat);
            },
            /**
             * RFC 2822 date
             * @return {string}
             */
            r: function () {
                return 'D, d M Y H:i:s O'.replace(backslash, doFormat);
            },
            /**
             * Seconds since UNIX epoch
             * @return {number}
             */
            U: function () {
                return vDate.getTime() / 1000 || 0;
            }
        };
        return doFormat(vChar, vChar);
    }
    formatDate(vDate, vFormat) {
        let i, n, len, str, vChar, vDateStr = '';
        const BACKSLASH = '\\';
        if (typeof vDate === 'string') {
            vDate = this.parseDate(vDate, vFormat);
            if (!vDate) {
                return null;
            }
        }
        if (vDate instanceof Date) {
            len = vFormat.length;
            for (i = 0; i < len; i++) {
                vChar = vFormat.charAt(i);
                if (vChar === 'S' || vChar === BACKSLASH) {
                    continue;
                }
                if (i > 0 && vFormat.charAt(i - 1) === BACKSLASH) {
                    vDateStr += vChar;
                    continue;
                }
                str = this.parseFormat(vChar, vDate);
                if (i !== (len - 1) && this.intParts.test(vChar) && vFormat.charAt(i + 1) === 'S') {
                    n = helper.getInt(str) || 0;
                    str += this.dateSettings.ordinal(n);
                }
                vDateStr += str;
            }
            return vDateStr;
        }
        return '';
    }
}

export default DateFormatter;
