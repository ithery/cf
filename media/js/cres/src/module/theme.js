import { addClass, hasClass, removeClass } from '../dom/classes';

export const toggleDarkMode = (localStorageKey) => {
    addClass(document.body, 'cres-theme-dark');
    removeClass(document.body, 'cres-theme-light');
    removeClass(document.body, 'cres-detect-theme');
    localStorage.setItem(localStorageKey, 'dark-mode');
};

export const toggleLightMode = (localStorageKey) => {
    addClass(document.body, 'cres-theme-light');
    removeClass(document.body, 'cres-theme-dark');
    removeClass(document.body, 'cres-detect-theme');
    localStorage.setItem(localStorageKey, 'light-mode');
};

export const enableAutoDetect = (localStorageKey) => {
    window.matchMedia('(prefers-color-scheme: dark)').addListener((event) => {
        return event.matches && toggleLightMode(localStorageKey);
    });
    window.matchMedia('(prefers-color-scheme: light)').addListener((event) => {
        return event.matches && toggleLightMode(localStorageKey);
    });
};

export const toggleAutoDetectMode = (localStorageKey) => {
    const isPreferDark = window.matchMedia(
        '(prefers-color-scheme: dark)'
    ).matches;
    const isPreferLight = window.matchMedia(
        '(prefers-color-scheme: light)'
    ).matches;
    const isNoPreference = window.matchMedia(
        '(prefers-color-scheme: no-preference)'
    ).matches;
    if (isPreferDark) {
        toggleDarkMode(localStorageKey);
    }
    if (isPreferLight) {
        toggleLightMode(localStorageKey);
    }
};
export const toggleMode = (localStorageKey) => {
    if (hasClass(document.body, 'cres-theme-light')) {
        toggleDarkMode(localStorageKey);
    } else {
        toggleLightMode(localStorageKey);
    }
};
export const initThemeMode = (localStorageKey) => {
    if (localStorage.getItem(localStorageKey) == 'dark-mode') {
        toggleDarkMode(localStorageKey);
    }
    if (localStorage.getItem(localStorageKey) == 'light-mode') {
        toggleLightMode(localStorageKey);
    }
    if (hasClass(document.body, 'cres-detect-theme')) {
        toggleAutoDetectMode(localStorageKey);
    }
};
