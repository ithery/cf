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


import './index.scss';
import Cresenity from './Cresenity';

window.Cresenity = Cresenity;
if (!window.cresenity) {
    window.cresenity = new Cresenity();
}
window.document.addEventListener('DOMContentLoaded', function () {
    window.cresenity.init();
});
