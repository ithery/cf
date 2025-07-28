let selectDataCache = {};
let lastValue = null;
let lastSelectDataString = null;
const addToCache = (newItems) => {
    for (const item of newItems) {
        const idStr = String(item.id);
        selectDataCache[idStr] = item;
    }
};
const buildConfigFromModifiers = (
    modifiers = [],
    evaluate = null,
    el = null
) => {
    const config = {};

    const getValue = (prefix) => {
        const found = modifiers.find((m) => m.startsWith(prefix + ':'));
        if (found && evaluate && el) {
            const rawValue = found.substring(prefix.length + 1);
            try {
                return evaluate(rawValue, { el });
            } catch {
                return rawValue; // fallback jika evaluasi gagal
            }
        }
        return undefined;
    };

    const has = (key) => modifiers.includes(key);

    if (has('multiple')) config.multiple = true;
    if (has('tags')) config.tags = true;
    if (has('allow-clear') || has('allowClear')) config.allowClear = true;

    const placeholder = getValue('placeholder');
    if (placeholder !== undefined) config.placeholder = placeholder;

    const language = getValue('language');
    if (language !== undefined) config.language = language;

    const perPage = getValue('per-page');
    if (perPage !== undefined) config.perPage = parseInt(perPage);

    const delay = getValue('delay');
    if (delay !== undefined) config.delay = parseInt(delay);

    const formatResult = getValue('format-result');
    if (formatResult !== undefined) config.formatResult = formatResult;

    const formatSelection = getValue('format-selection');
    if (formatSelection !== undefined) config.formatSelection = formatSelection;

    return config;
};
const buildSelect2Options = (selectData) => {
    let options = {};
    options.width = '100%';
    options.language = selectData.language;
    options.allowClear = selectData.allowClear ? 'true' : 'false';
    options.placeholder = selectData.placeholder;
    options.multiple = selectData.multiple;
    let ajaxOptions = {};
    ajaxOptions.url = selectData.ajaxUrl;
    ajaxOptions.dataType = 'jsonp';
    ajaxOptions.quietMillis = selectData.delay;
    ajaxOptions.delay = selectData.delay;
    ajaxOptions.multiple = selectData.multiple;

    ajaxOptions.data = (params) => {
        let result = {
            q: params.term, // search term
            page: params.page,
            limit: selectData.perPage
        };
        return result;
    };
    ajaxOptions.processResults = (data, params) => {
        addToCache(data.data);;
        params.page = params.page || 1;
        var more = params.page * selectData.perPage < data.total;
        return {
            results: data.data,
            pagination: {
                more: more
            }
        };
    };
    ajaxOptions.cache = true;
    ajaxOptions.error = function (jqXHR, status, error) {
        if (cresenity && cresenity.handleAjaxError) {
            cresenity.handleAjaxError(jqXHR, status, error);
        }
    };
    options.ajax = ajaxOptions;
    let selectedData = selectData.selectedData;

    if (selectedData) {
        if (Array.isArray(selectedData) && selectedData.length > 0) {
            if (selectData.multiple == false) {
                selectedData = selectedData[0]; // ambil elemen pertama
            }

            options.initSelection = function (element, callback) {
                const data = selectedData;
                callback(data);
            };
        }
    }

    options.templateResult = (item) => {
        if (typeof item.loading !== 'undefined') {
            return item.text;
        }
        if (item.cappFormatResult) {
            if (item.cappFormatResultIsHtml) {
                return $('<div>' + item.cappFormatResult + '</div>');
            } else {
                return item.cappFormatResult;
            }
        }

        return $('<div>' + selectData.formatResult + '</div>');
    };
    options.templateSelection = (item) => {
        if (item.element) {
            let dataMultiple = $(item.element).attr('data-multiple');
            if (dataMultiple == '0') {
                let dataContent = $(item.element).attr('data-content');

                if (dataContent) {
                    if (/<\/?[a-z][\s\S]*>/i.test(dataContent)) {
                        return $(dataContent);
                    }
                    return dataContent;
                }
            } else {
                let dataContent = $(item.element).attr('data-content');

                if (dataContent) {
                    if (/<\/?[a-z][\s\S]*>/i.test(dataContent)) {
                        return $(dataContent);
                    }
                    return dataContent;
                }
                if (item.text) {
                    return item.text;
                }
            }
        }

        if (item.cappFormatSelection) {
            if (item.cappFormatSelectionIsHtml) {
                return $('<div>' + item.cappFormatSelection + '</div>');
            } else {
                return item.cappFormatSelection;
            }
        }
        if (item.id === '' && item.text) {
            return item.text;
        }
        let strSelection = selectData.formatSelection;

        if (!strSelection) {
            let searchFieldText = selectData.searchField?.[0];

            if (
                typeof searchFieldText === 'string' &&
                searchFieldText.length > 0
            ) {
                strSelection = item[searchFieldText];
            }
        }
        let htmlResult = strSelection;
        if (htmlResult === 'undefined') {
            return item.text;
        }

        return $('<div>' + htmlResult + '</div>');
    };
    return options;
};



const valueChangeCallback = (el) => {
    return () => {
        let value = $(el).val();
        if (!el._x_model) {
            return;
        }

        el._x_model.set(value);
    };
};

const escapeHtml = (str) => {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
};

const createOption = (item, selectData) => {
    const selectedValue = item.id;
    const strSelection =
        item.cappFormatSelection ?? item.text;
    const optionEl = $(
        '<option data-multiple="' +
            (selectData.multiple ? '1' : '0') +
            '" value="' +
            selectedValue +
            '" data-content="' +
            escapeHtml(strSelection) +
            '" selected="selected" >' +
            strSelection +
            '</option>'
    );
    return optionEl;
}
const jsonpRequest = (url, data) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: url,
            data: data,
            dataType: 'jsonp',
            success: resolve,
            error: reject
        });
    });
};
const loadSelectedOption = async (value, el, selectData) => {
    // console.log('loadSelectedOption', value, el, selectData);
    if (!value || (Array.isArray(value) && value.length === 0)) return;
    const values = Array.isArray(value) ? value : [value];
    const url = selectData.ajaxUrl;
    if (!url) return;
    const missingIds = values.filter(v => !(String(v) in selectDataCache));
    const renderOptions = (items) => {
        $(el).find('option').remove();

        for (const val of values) {
            const item = items.find(i => String(i.id) === String(val));
            if (item) {
                const $opt = createOption(item, selectData);
                $opt.prop('selected', true);
                $(el).append($opt);
            }
        }
    };

    if (missingIds.length > 0) {
        try {
            const response = await jsonpRequest(url, { id: missingIds });
            const newItems = response.data ?? [];
            for (const item of newItems) {
                const idStr = String(item.id);
                selectDataCache[idStr] = item;
            }
        } catch (err) {
            console.error("Failed to load via JSONP:", err);
        }
    }
    const matchedItems = values
        .map(v => selectDataCache[String(v)])
        .filter(Boolean);
    renderOptions(matchedItems);
};

export default function (Alpine) {
    Alpine.directive(
        'select2',
        (el, { modifiers, expression }, { evaluate, effect, cleanup }) => {
            if (el.__select2Initialized) {
                return;
            }
            el.__select2Initialized = true;
            const originalData = evaluate(expression);
            const modifierConfig = buildConfigFromModifiers(modifiers, Alpine.evaluate, el);
            const selectData = { ...originalData, ...modifierConfig };
            const select2Config = buildSelect2Options(selectData);
            if (select2Config.multiple) {
                el.setAttribute('multiple', 'multiple');
            } else {
                el.removeAttribute('multiple');
            }
             // Setup Select2
            const instance = $(el).select2(select2Config);
            if (el._x_model) {
                const value = el._x_model?.get?.();
                loadSelectedOption(value, el, selectData);
                if(!$(el).val()) {
                    $(el).val(value).trigger('change');
                }
            } else {
                const selectedData = selectData.selectedData;
                if (selectedData && selectedData.length > 0) {
                    $(el).find('option').remove();
                    let selectedValue = selectData.multiple ? [] : null;
                    for (const item of selectedData) {
                        $(el).append(createOption(item, selectData));
                        selectedData.multiple ? selectedValue.push(item.id) : selectedValue = item.id;
                    }
                    $(el).val(selectedValue).trigger('change');
                }
            }

            $(el).on('change.select2 select2:select select2:unselect', valueChangeCallback(el));
            valueChangeCallback(el)();
            // x-model support
            if (el._x_model) {
                let modelLoaded = false;
                effect(() => {
                    if (modelLoaded) return;
                    Alpine.mutateDom(() => {
                        const value = el._x_model?.get?.() ?? (el.multiple ? [] : '');
                        loadSelectedOption(value, el, selectData);
                    });
                    modelLoaded = true;
                });
            }
            if (el._x_bindings && el._x_bindings.value) {
                let bindingLoaded = false;

                effect(() => {
                    if (bindingLoaded) return;
                    Alpine.mutateDom(() => {
                        const value = el._x_bindings.value ?? (el.multiple ? [] : '');
                        loadSelectedOption(value, el, selectData);
                    });
                    bindingLoaded = true;
                });
            }
            cleanup(() => {
                $(el).select2('destroy');
                el.__select2Initialized = false;
            });
        }
    );
}
