// Localise Globals
// @see https://github.com/browserstate/history.js

import { initAdapter } from './adapter';
import { initCore } from './core';


export const initHistory = (History) => {
    // Initialise History
    History.init = function (options) {
        // Check Load Status of Adapter
        if (typeof History.Adapter === 'undefined') {
            return false;
        }

        // Check Load Status of Core
        if (typeof History.initCore !== 'undefined') {
            History.initCore();
        }

        // Check Load Status of HTML4 Support
        if (typeof History.initHtml4 !== 'undefined') {
            History.initHtml4();
        }

        // Return true
        return true;
    };
    initAdapter(History);
    initCore(History);
};
