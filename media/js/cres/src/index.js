
String.prototype.contains = function (a) {
    return !!~this.indexOf(a);
};
String.prototype.toNumber = function () {
    var n = parseFloat(this);
    if (!isNaN(n)) {
        return n;
    } else {
        return 0;
    }
}

import "./index.css";
import Cresenity from '@/Cresenity'


window.Cresenity = Cresenity;
