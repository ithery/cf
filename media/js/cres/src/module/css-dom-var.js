import { domReady } from "../util/dom-observer"

const updateWindowSize = () => {
    if(document) {
        if (document.readyState === "interactive" || document.readyState === "complete" ) {
            const doc = document.documentElement
            doc.style.setProperty('--cres-window-height', `${window.innerHeight}px`)
            doc.style.setProperty('--cres-window-width', `${window.innerWidth}px`)
        }
    }
}

export const initCssDomVar = () => {
    domReady(updateWindowSize);
    window.addEventListener('resize', updateWindowSize);
}
