import extend from '../core/extend';

const POLL_INTERVAL = 3000;

const SPINNER_HTML = '<div class="sk-fading-circle sk-primary">'
    + '<div class="sk-circle1 sk-circle"></div>'
    + '<div class="sk-circle2 sk-circle"></div>'
    + '<div class="sk-circle3 sk-circle"></div>'
    + '<div class="sk-circle4 sk-circle"></div>'
    + '<div class="sk-circle5 sk-circle"></div>'
    + '<div class="sk-circle6 sk-circle"></div>'
    + '<div class="sk-circle7 sk-circle"></div>'
    + '<div class="sk-circle8 sk-circle"></div>'
    + '<div class="sk-circle9 sk-circle"></div>'
    + '<div class="sk-circle10 sk-circle"></div>'
    + '<div class="sk-circle11 sk-circle"></div>'
    + '<div class="sk-circle12 sk-circle"></div>'
    + '</div>';

function buildInitialStatus(cresenity, interval) {
    let wrapper = $('<div>');
    let label = $('<label>', {class: 'mb-4'}).append('Please Wait...');
    let animation = $('<div class="cres-download-progress-animation">').append(SPINNER_HTML);
    let actionContainer = $('<div>', {class: 'text-center my-3'});
    let cancelButton = $('<button>', {class: 'btn btn-primary'}).append('Cancel');

    cancelButton.click(function () {
        clearInterval(interval);
        cresenity.closeLastModal();
    });

    actionContainer.append(cancelButton);
    wrapper.append(label).append(animation).append(actionContainer);

    return wrapper;
}

function renderDone(cresenity, container, data, interval) {
    clearInterval(interval);

    let statusEl = container.find('.cres-download-progress-status');
    statusEl.empty();

    let label = $('<label>', {class: 'mb-3 d-block'}).append('Your file is ready');
    let downloadLink = $('<a>', {
        target: '_blank',
        href: data.fileUrl,
        class: 'btn btn-primary'
    }).append('Download');
    let closeLink = $('<a>', {
        href: 'javascript:;',
        class: 'btn btn-primary ml-3'
    }).append('Close');

    closeLink.click(function () {
        cresenity.closeLastModal();
    });

    statusEl.append($('<div>').append(label).append(downloadLink).append(closeLink));
}

function renderPending(container, data) {
    let progressValue = parseFloat(data.progressValue);
    if (!(progressValue > 0)) {
        return;
    }

    let statusBar = container.find('.cres-download-progress-status-bar');
    if (statusBar.length === 0) {
        let animationEl = container.find('.cres-download-progress-animation');
        animationEl.empty();

        statusBar = $('<div class="cres-download-progress-status-bar my-4">');
        let progress = $('<div class="progress">');
        let progressBar = $('<div class="progress-bar progress-bar-striped progress-bar-animated">');
        animationEl.append(statusBar.append(progress.append(progressBar)));
    }

    let progressMax = parseFloat(data.progressMax);
    if (isNaN(progressMax) || progressMax === 0) {
        progressMax = 100;
    }

    let progressBar = statusBar.find('.progress-bar');
    let percent = Math.round(progressMax > 0 ? progressValue * 100 / progressMax : 0);
    progressBar.css('width', percent + '%');
    progressBar.html(percent + '%');
}

function pollProgress(cresenity, progressUrl, method, container, interval) {
    $.ajax({
        type: method,
        url: progressUrl,
        dataType: 'json',
        success: function (response) {
            cresenity.handleJsonResponse(response, function (data) {
                if (data.state === 'DONE') {
                    renderDone(cresenity, container, data, interval);
                } else if (data.state === 'PENDING') {
                    renderPending(container, data);
                }
            });
        }
    });
}

function showProgressModal(cresenity, progressUrl, method) {
    let container = $('<div>').addClass('cres-download-progress');

    let interval = setInterval(function () {
        pollProgress(cresenity, progressUrl, method, container, interval);
    }, POLL_INTERVAL);

    let statusEl = buildInitialStatus(cresenity, interval);
    container.append(
        $('<div class="text-center">').addClass('cres-download-progress-status').append(statusEl)
    );

    cresenity.modal({
        message: container,
        modalClass: 'cres-modal-download-progress'
    });
}

export default class DownloadProgress {
    constructor(cresenity) {
        this.cresenity = cresenity;
    }

    start(options) {
        let cresenity = this.cresenity;
        let settings = extend({
            method: 'get',
            dataAddition: {},
            url: '/',
            onComplete: false,
            onSuccess: false,
            onBlock: false,
            onUnblock: false
        }, options);

        let url = cresenity.url.replaceParam(settings.url);
        let dataAddition = settings.dataAddition || {};

        if (typeof settings.onBlock === 'function') {
            settings.onBlock();
        } else {
            cresenity.blockPage();
        }

        let xhr = jQuery(window).data('cappXhrProgress');
        if (xhr) {
            xhr.abort();
        }

        $.ajax({
            type: settings.method,
            url: url,
            dataType: 'json',
            data: dataAddition,
            success: function (response) {
                cresenity.handleJsonResponse(response, function (data) {
                    showProgressModal(cresenity, data.progressUrl, settings.method);
                });
            },
            error: function (xhrError, ajaxOptions, thrownError) {
                if (thrownError !== 'abort') {
                    cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                }
            },
            complete: function () {
                if (typeof settings.onUnblock === 'function') {
                    settings.onUnblock();
                } else {
                    cresenity.unblockPage();
                }

                if (typeof settings.onComplete === 'function') {
                    settings.onComplete();
                }
            }
        });
    }
}
