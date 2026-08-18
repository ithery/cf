/* eslint-disable no-underscore-dangle */

import * as utils from '../util/helper';

const alpineReload = (elHost, urls, debug = false) => {
    const unwrap = elHost._cui_unwrap;
    let baseUrl;
    for (let elCurrent = elHost; elCurrent; elCurrent = elCurrent.parentElement) {
        baseUrl = elCurrent._cui_base_url;
        if (baseUrl) {break;}
    }
    if (!baseUrl) {baseUrl = document.baseURI;}
    if (utils.isArray(urls)) {
        utils.each(urls, url => {
            url = url.trim();
            let fullUrl = new URL(url, baseUrl).href;
            window.cresenity.reload({
                selector: elHost,
                url: fullUrl
            });
        });
    }

    // if (utils.isArray(urls)) {
    //     const tasks = [];
    //     utils.each(urls, url => {
    //         url = url.trim();
    //         if (url) {
    //             let fullUrl = new URL(url, baseUrl).href;
    //             tasks.push(fetch(fullUrl).then(r => r.text()).then(html => {
    //                 const el = document.createElement('div');
    //                 el._x_ignore = true;
    //                 el.innerHTML = html;
    //                 let all = [...el.childNodes];
    //                 return new Promise((resolve) => {
    //                     const process = (i) => {
    //                         if (i < all.length) {
    //                             const elChild = all[i];
    //                             elChild.remove();
    //                             if (elChild.tagName === 'SCRIPT') {
    //                                 const elExecute = document.createElement('script');
    //                                 const wait = elChild.src && !elChild.async;
    //                                 if (wait) {
    //                                     elExecute.onload = () => {
    //                                         process(i + 1);
    //                                     };
    //                                     elExecute.onerror = () => {
    //                                         console.error(`Fails to load script from "${elExecute.src}"`);
    //                                         process(i + 1);
    //                                     };
    //                                 }
    //                                 _.each(elChild.attributes, a => elExecute.setAttribute(a.name, a.value));
    //                                 if (!elChild.src) {
    //                                     let file = `__vui__/scripts/js_${$vui.importScriptIndex}.js`;
    //                                     elExecute.setAttribute('file', file);
    //                                     elExecute.innerHTML = `${elChild.innerHTML}\r\n//From ${url}\r\n//# sourceURL=${file}`;
    //                                     $vui.importScriptIndex++;
    //                                 }
    //                                 document.body.append(elExecute);
    //                                 if (!wait) {process(i + 1);}
    //                             } else {
    //                                 elChild._vui_base_url = fullUrl;
    //                                 if (unwrap) {
    //                                     elHost.before(elChild);
    //                                 } else {
    //                                     elHost.append(elChild);
    //                                 }
    //                                 process(i + 1);
    //                             }
    //                         } else {
    //                             if (debug) {console.log(`Reloaded ${url}`);}
    //                             if (unwrap) {elHost.remove();}
    //                             resolve();
    //                         }
    //                     };
    //                     process(0);
    //                 });
    //             }).catch(ex => {
    //                 console.error(`Fails to include ${comp} @ ${url}`, ex);
    //             }));
    //         }
    //     });
    //     return Promise.all(tasks);
    // }
    // return Promise.reject(`Fails to include ${urls} !`);
};


export default function (Alpine) {
    const { directive, prefixed, addRootSelector } = Alpine;
    const debug = window.capp?.alpine?.ui?.debug ?? false;
    addRootSelector(() => `[${prefixed('reload')}]`);
    directive('reload', (el, { expression, modifiers }, { effect, evaluateLater }) => {
        if (!expression) {return;}
        // eslint-disable-next-line no-underscore-dangle
        el._cui_unwrap = modifiers.includes('unwrap');
        let urls = expression.trim();
        if (urls.startsWith('.') || urls.startsWith('/') || urls.startsWith('http://') || urls.startsWith('https://')) {
            alpineReload(el, [urls]);
        } else {
            let evaluate = evaluateLater(expression);
            effect(() => evaluate(value => {
                if (utils.isArray(value)) {
                    alpineReload(el, value);
                } else if (utils.isString(value)) {
                    alpineReload(el, [value]);
                } else {
                    alpineReload(el, [urls]);
                }
            }));
        }
    });
}
