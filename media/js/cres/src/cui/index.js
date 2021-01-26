import DOM from '@/cui/dom/dom';
import '@/cui/dom/polyfills/index';
import store from '@/cui/Store';
import Connection from '@/cui/connection';
import Polling from '@/cui/component/Polling';
import Component from '@/cui/component/index';
import { dispatch, cfDirectives } from '@/util';
import FileUploads from '@/cui/component/FileUploads';
import LaravelEcho from '@/cui/component/LaravelEcho';
import DirtyStates from '@/cui/component/DirtyStates';
import DisableForms from '@/cui/component/DisableForms';
import FileDownloads from '@/cui/component/FileDownloads';
import LoadingStates from '@/cui/component/LoadingStates';
import OfflineStates from '@/cui/component/OfflineStates';
import SyncBrowserHistory from '@/cui/component/SyncBrowserHistory';
import SupportAlpine from '@/cui/component/SupportAlpine';

class CUI {
    constructor() {
        this.connection = new Connection();
        this.components = store;
        this.devToolsEnabled = false;
        this.onLoadCallback = () => {};
    }

    first() {
        return Object.values(this.components.componentsById)[0].$cf;
    }

    find(componentId) {
        return this.components.componentsById[componentId].$cf;
    }

    all() {
        return Object.values(this.components.componentsById).map(
            component => component.$cf
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
        dispatch('cresenity:load');

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
            const componentId = cfDirectives(el).get('id').value;

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

export default CUI;
