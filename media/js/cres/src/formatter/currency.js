export const formatCurrency = (rp) => {
    rp = '' + rp;
    let rupiah = '';
    let vfloat = '';
    let ds = window.capp?.format?.decimalSeparator ?? '.';
    let ts = window.capp?.format?.thousandSeparator ?? ',';
    let dd = window.capp?.format?.decimalDigit ?? 2;
    dd = parseInt(dd, 10);
    let minusStr = '';
    if (rp.indexOf('-') >= 0) {
        minusStr = rp.substring(rp.indexOf('-'), 1);
        rp = rp.substring(rp.indexOf('-') + 1);
    }

    if (rp.indexOf('.') >= 0) {
        vfloat = rp.substring(rp.indexOf('.'));
        rp = rp.substring(0, rp.indexOf('.'));
    }
    let p = rp.length;
    while (p > 3) {
        rupiah = ts + rp.substring(p - 3) + rupiah;
        let l = rp.length - 3;
        rp = rp.substring(0, l);
        p = rp.length;
    }
    rupiah = rp + rupiah;
    vfloat = vfloat.replace('.', ds);
    if (vfloat.length > dd) {
        vfloat = vfloat.substring(0, dd + 1);
    }
    return minusStr + rupiah + vfloat;
};

export const unformatCurrency = (rp) => {
    const replaceAll = (string, find, replace) => {
        let escapedFind = find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1');
        return string.replace(new RegExp(escapedFind, 'g'), replace);
    }

    if (typeof rp == 'undefined') {
        rp = '';
    }
    let ds = window.capp?.format?.decimalSeparator ?? '.';
    let ts = window.capp?.format?.thousandSeparator ?? ',';
    rp = replaceAll(rp, ts, '');

    rp = rp.replace(ds, '.');
    return rp;
};
