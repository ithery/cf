const keyStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
const utf8Encode = (e) => {
    e = e.replace(/rn/g, 'n');
    let t = '';
    for (let n = 0; n < e.length; n++) {
        let r = e.charCodeAt(n);
        if (r < 128) {
            t = t + String.fromCharCode(r);
        } else if (r > 127 && r < 2048) {
            t = t + String.fromCharCode(r >> 6 | 192);
            t = t + String.fromCharCode(r & 63 | 128);
        } else {
            t = t + String.fromCharCode(r >> 12 | 224);
            t = t + String.fromCharCode(r >> 6 & 63 | 128);
            t = t + String.fromCharCode(r & 63 | 128);
        }
    }
    return t;
};

const utf8Decode = (e) => {
    let t = '';
    let n = 0;
    let c2 = 0;
    let c3 = 0;
    let r = 0;
    while (n < e.length) {
        r = e.charCodeAt(n);
        if (r < 128) {
            t = t + String.fromCharCode(r);
            n++;
        } else if (r > 191 && r < 224) {
            c2 = e.charCodeAt(n + 1);
            t = t + String.fromCharCode((r & 31) << 6 | c2 & 63);
            n = n + 2;
        } else {
            c2 = e.charCodeAt(n + 1);
            c3 = e.charCodeAt(n + 2);
            t = t + String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
            n = n + 3;
        }
    }
    return t;
};
const encode = (e) => {
    let t = '';
    let n, r, i, s, o, u, a;
    let f = 0;
    e = utf8Encode(e);
    while (f < e.length) {
        n = e.charCodeAt(f++);
        r = e.charCodeAt(f++);
        i = e.charCodeAt(f++);
        s = n >> 2;
        o = (n & 3) << 4 | r >> 4;
        u = (r & 15) << 2 | i >> 6;
        a = i & 63;
        if (isNaN(r)) {
            u = a = 64;
        } else if (isNaN(i)) {
            a = 64;
        }
        t = t + keyStr.charAt(s) + keyStr.charAt(o) + keyStr.charAt(u) + keyStr.charAt(a);
    }
    return t;
};

const decode = (e) => {
    let t = '';
    let n, r, i;
    let s, o, u, a;
    let f = 0;
    e = e.replace(/[^A-Za-z0-9+/=]/g, '');
    while (f < e.length) {
        s = keyStr.indexOf(e.charAt(f++));
        o = keyStr.indexOf(e.charAt(f++));
        u = keyStr.indexOf(e.charAt(f++));
        a = keyStr.indexOf(e.charAt(f++));
        n = s << 2 | o >> 4;
        r = (o & 15) << 4 | u >> 2;
        i = (u & 3) << 6 | a;
        t = t + String.fromCharCode(n);
        if (u != 64) {
            t = t + String.fromCharCode(r);
        }
        if (a != 64) {
            t = t + String.fromCharCode(i);
        }
    }
    t = utf8Decode(t);
    return t;
};

export {
    encode, decode
};
