let Dotenv = require('dotenv');
class Pod {
     /** @type {Pod|null} */
     static _primary = null;

     /**
     * Create a new instance.
     */
    constructor() {
        this.registrar = new ComponentRegistrar(this);
    }
    /**
     * @internal
     */
    static get primary() {
        return Pod._primary || (Pod._primary = new Pod());
    }
    get api() {
        if (!this._api) {
            this._api = this.registrar.installAll();

            // @ts-ignore
            this._api.inProduction = () => this.config.production;
        }

        // @ts-ignore
        return this._api;
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
