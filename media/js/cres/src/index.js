// eslint-disable-next-line no-extend-native
String.prototype.contains = function (a) {
    return !!~this.indexOf(a);
};

// eslint-disable-next-line no-extend-native
String.prototype.toNumber = function () {
    let n = parseFloat(this);
    if (!isNaN(n)) {
        return n;
    }
    return 0;
};


import './index.css';
import Cresenity from '@/Cresenity';

window.Cresenity = Cresenity;
window.cresenity = new Cresenity();
