import { debounce } from '../../util/debounce';

function getValueFromSelector(selector) {
    let $el = $(selector);
    if ($el.length === 0) {
        return null;
    }
    if ($el.is(':checkbox')) {
        return $(selector + ':checked').val();
    }
    return $el.val();
}

function getValueFromSelectors(selectors) {
    if (selectors.length === 1) {
        return getValueFromSelector(selectors[0]);
    }
    let values = [];
    for (let i = 0; i < selectors.length; i++) {
        values.push(getValueFromSelector(selectors[i]));
    }
    return values;
}

function runDependsOn(config) {
    let value = getValueFromSelectors(config.selectors);

    let ajaxOptions = {
        url: config.ajaxUrl,
        method: 'post',
        dataAddition: {value: value},
        handleJsonResponse: true,
        onSuccess: function (data) {
            let $target = $(config.targetSelector);

            if (config.type === 'content') {
                window.cresenity.handleResponse(data, function () {
                    $target.empty();
                    if (typeof data === 'object') {
                        if (data.value) {
                            $target.html(data.value);
                        } else {
                            if (typeof data.html === 'undefined') {
                                window.cresenity.htmlModal(data);
                            } else {
                                $target.html(data.html);
                                if (data.js && data.js.length > 0) {
                                    let script = window.cresenity.base64.decode(data.js);
                                    eval(script);
                                }
                            }
                        }
                    } else {
                        $target.html(data);
                    }
                });
            } else {
                if (typeof data === 'object') {
                    if (typeof data.value !== 'undefined') {
                        $target.val(data.value);
                    } else {
                        $target.val(JSON.stringify(data));
                    }
                } else {
                    $target.val(data);
                }
            }
        }
    };

    if (!config.block) {
        ajaxOptions.block = false;
    }

    window.cresenity.ajax(ajaxOptions);
}

export function initDependsOn(dependsOnConfigs) {
    if (!dependsOnConfigs || dependsOnConfigs.length === 0) {
        return;
    }

    for (let i = 0; i < dependsOnConfigs.length; i++) {
        let config = dependsOnConfigs[i];
        let selectorStr = config.selectors.join(', ');
        let handler = debounce(function () {
            runDependsOn(config);
        }, config.throttle || 100);

        $(selectorStr).on('change', handler);
        runDependsOn(config);
    }
}
