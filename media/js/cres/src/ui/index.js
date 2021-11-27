import DOM from '@/ui/dom/dom';
import '@/ui/dom/polyfills/index';
import store from '@/ui/Store';
import Connection from '@/ui/connection';
import Polling from '@/ui/component/Polling';
import Component from '@/ui/component/index';
import { dispatch, cresDirectives } from '@/util';
import FileUploads from '@/ui/component/FileUploads';
import LaravelEcho from '@/ui/component/LaravelEcho';
import DirtyStates from '@/ui/component/DirtyStates';
import DisableForms from '@/ui/component/DisableForms';
import FileDownloads from '@/ui/component/FileDownloads';
import LoadingStates from '@/ui/component/LoadingStates';
import OfflineStates from '@/ui/component/OfflineStates';
import SyncBrowserHistory from './component/SyncBrowserHistory';
import SupportAlpine from '@/ui/component/SupportAlpine';
import { attachWaves } from './waves';

class UI {
    constructor() {
        this.connection = new Connection();
        this.components = store;
        this.devToolsEnabled = false;
        this.onLoadCallback = () => {};
        this.waves = {
            attach: attachWaves
        };
    }

    first() {
        return Object.values(this.components.componentsById)[0].$cres;
    }

    find(componentId) {
        return this.components.componentsById[componentId].$cres;
    }

    all() {
        return Object.values(this.components.componentsById).map(
            component => component.$cres
        );
    }

    directive(name, callback) {
        this.components.registerDirective(name, callback);
    }

    hook(name, callback) {
        this.components.registerHook(name, callback);
    }

    onLoad(callback) {
        this.onLoadCallback = callback;
    }

    onError(callback) {
        this.components.onErrorCallback = callback;
    }

    emit(event, ...params) {
        this.components.emit(event, ...params);
    }

    emitTo(name, event, ...params) {
        this.components.emitTo(name, event, ...params);
    }

    on(event, callback) {
        this.components.on(event, callback);
    }

    off(event, callback) {
        this.components.off(event, callback);
    }

    devTools(enableDevtools) {
        this.devToolsEnabled = enableDevtools;
    }

    restart() {
        this.stop();
        this.start();
    }

    stop() {
        this.components.tearDownComponents();
    }

    start() {
        DOM.rootComponentElementsWithNoParents().forEach(el => {
            this.components.addComponent(new Component(el, this.connection));
        });

        this.onLoadCallback();
        dispatch('cresenity:ui:start');

        document.addEventListener(
            'visibilitychange',
            () => {
                this.components.cresenityIsInBackground = document.hidden;
            },
            false
        );

        this.components.initialRenderIsFinished = true;
    }

    rescan(node = null) {
        DOM.rootComponentElementsWithNoParents(node).forEach(el => {
            const componentId = cresDirectives(el).get('id').value;

            if (this.components.hasComponent(componentId)) {return;}

            this.components.addComponent(new Component(el, this.connection));
        });
    }
}


SyncBrowserHistory();
SupportAlpine();
FileDownloads();
OfflineStates();
LoadingStates();
DisableForms();
FileUploads();
LaravelEcho();
DirtyStates();
Polling();

export default UI;
