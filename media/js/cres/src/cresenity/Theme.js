import slug from '../helper/str/slug';
import { toggleDarkMode, toggleLightMode, toggleMode, toggleAutoDetectMode, enableAutoDetect } from '../module/theme';

export default class Theme {
    constructor() {
        this.name = window.capp.theme?.name ?? 'cresenity';
        this.modules = window.capp.theme?.data.client_modules ?? [];
        this.css = window.capp.theme?.css ?? [];
        this.js = window.capp.theme?.js ?? [];
        this.js = window.capp.theme?.data ?? {};
    }

    get slug() {
        return slug(this.name, {delimiter: '-'});
    }
    get localStorageKey() {
        return this.slug + '-cres-theme';
    }
    toggleDarkMode() {
        toggleDarkMode(this.localStorageKey);
    }

    toggleLightMode() {
        toggleLightMode(this.localStorageKey);
    }

    enableAutoDetect() {
        enableAutoDetect(this.localStorageKey);
    }

    toogleAutoDetectMode() {
        toggleAutoDetectMode(this.localStorageKey);
    }
    toggleMode() {
        toggleMode(this.localStorageKey);
    }
}
