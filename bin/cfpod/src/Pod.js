let Dotenv = require('dotenv');
class Pod {
     /** @type {Pod|null} */
     static _primary = null;

     /**
     * Create a new instance.
     */
    constructor() {

    }
    /**
     * @internal
     */
    static get primary() {
        return Pod._primary || (Pod._primary = new Pod());
    }
     /**
     * @internal
     */
     boot() {
        if (this.booted) {
            return this;
        }

        this.booted = true;



        return this;
    }


}
