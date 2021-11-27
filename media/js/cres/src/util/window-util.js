import { isJson } from './helper';

export const toggleFullscreen = (element) => {
    if(!element) {
        element = document.documentElement;
    }
    if (!$('body').hasClass('full-screen')) {
        $('body').addClass('full-screen');
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    } else {
        $('body').removeClass('full-screen');
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    }
};


const initIframeModal = () => {
    let modal = document.getElementById('capp-html-modal');

    if (typeof modal != 'undefined' && modal != null) {
        // Modal already exists.
        modal.innerHTML = '';
    } else {
        let iframe = document.createElement('iframe');
        iframe.style.backgroundColor = '#17161A';
        iframe.style.borderRadius = '5px';
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        modal = document.createElement('div');
        modal.id = 'capp-html-modal';
        modal.style.position = 'fixed';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.padding = '50px';
        modal.style.backgroundColor = 'rgba(0, 0, 0, .6)';
        modal.style.zIndex = 200000;
        // Close on click.
        modal.addEventListener('click', () => hideIframeModal(modal));

        // Close on escape key press.
        modal.setAttribute('tabindex', 0);
        modal.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                hideIframeModal(modal);
            }
        });
        document.body.prepend(modal);
        modal.appendChild(iframe);
    }

    let iframe = modal.firstChild;
    iframe.contentWindow.document.innerHTML = '';
    document.body.style.overflow = 'hidden';
    modal.focus();

    return modal;
};

export const showHtmlModal = (html) => {
    let isHtmlJson = false;
    if(isJson(html)) {
        html = JSON.parse(html);
    }
    if(typeof html ==='object') {
        html = JSON.stringify(html, null, 4);
        html = '<pre style="background-color:#fff">' + html + '</pre>';
        isHtmlJson = true;
    }

    let page = document.createElement('html');
    page.innerHTML = html;
    page.querySelectorAll('a').forEach(a =>
        a.setAttribute('target', '_top')
    );

    let modal = initIframeModal();
    let iframe = modal.firstChild;

    iframe.contentWindow.document.open();
    iframe.contentWindow.document.write(page.outerHTML);
    iframe.contentWindow.document.close();

    return modal;
};

export const showUrlModal = (url) => {
    let modal = initIframeModal();
    let iframe = modal.firstChild;

    iframe.src = url;
    return modal;
};


export const hideIframeModal = (modal) => {
    if(typeof modal == 'undefined') {
        modal = document.getElementById('capp-html-modal');
    }
    if(typeof modal != 'undefined') {
        modal.outerHTML = '';
        document.body.style.overflow = 'visible';
    }
};
