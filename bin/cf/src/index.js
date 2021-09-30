const CF = require('./CF');

require('./helpers');



let cf = CF.instance;

cf.boot();

module.exports = cf.api;
