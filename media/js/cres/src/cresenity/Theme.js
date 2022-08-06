import slug from '../helper/str/slug';
import { toggleDarkMode, toggleLightMode, toggleMode, toggleAutoDetectMode, enableAutoDetect } from '../module/theme';

export default class Theme {
    constructor() {
        this.name = capp.theme?.name ?? 'cresenity';
        this.modules = capp.theme?.data.client_modules ?? [],
        this.css = capp.theme?.css ?? [];
        this.js = capp.theme?.js ?? [];
        this.js = capp.theme?.data ?? {};

    }

    get slug() {
        return slug(this.name, {delimiter:'-'});
    }
    get localStorageKey() {
        return this.slug + '-cres-theme';
    }
    toggleDarkMode() {
        toggleDarkMode(this.localStorageKey);
    }

    toggleLightMode = () => {
        toggleLightMode(this.localStorageKey);

    };

    enableAutoDetect() {
        enableAutoDetect(this.localStorageKey);
    };

    toogleAutoDetectMode() {
        toggleAutoDetectMode(this.localStorageKey);
    }
    toggleMode() {
        toggleMode(this.localStorageKey);
    }



}
