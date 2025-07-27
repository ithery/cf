const buildSelect2Options = (selectData) =>{
    let options = {};
    options.width='100%';
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

    ajaxOptions.data =  (params) => {
        let result = {
            q: params.term, // search term
            page: params.page,
            limit: selectData.perPage
        };
        return result;
    },

    ajaxOptions.processResults = (data, params) => {
        params.page = params.page || 1;
        var more = (params.page * selectData.perPage) < data.total;
        return {
            results: data.data,
            pagination: {
                more: more
            }
        };
    },
    ajaxOptions.cache = true;
    ajaxOptions.error = function (jqXHR, status, error) {
        if(cresenity && cresenity.handleAjaxError) {
            cresenity.handleAjaxError(jqXHR, status, error);
        }
    }
    options.ajax = ajaxOptions;
    let selectedData = selectData.selectedData;

    if(selectedData) {
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
        if(item.cappFormatResult) {
            if(item.cappFormatResultIsHtml) {
                return $('<div>' + item.cappFormatResult +'</div>');
            } else {
                return item.cappFormatResult;
            }
        }

        return $('<div>' + selectData.formatResult + '</div>');
    };
    options.templateSelection = (item) => {
        if(item.element) {
            let dataMultiple = $(item.element).attr('data-multiple');
            if(dataMultiple == '0') {
                let dataContent = $(item.element).attr('data-content');

                if(dataContent) {
                    if(/<\/?[a-z][\s\S]*>/i.test(dataContent)) {
                        return $(dataContent);
                    }
                    return dataContent;
                }
            } else {
                let dataContent = $(item.element).attr('data-content');

                if(dataContent) {
                    if(/<\/?[a-z][\s\S]*>/i.test(dataContent)) {
                        return $(dataContent);
                    }
                    return dataContent;
                }
                if(item.text){
                    return item.text;
                }
            }
        }

        if(item.cappFormatSelection) {

            if(item.cappFormatSelectionIsHtml) {
                return $('<div>' + item.cappFormatSelection +'</div>');
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

            if (typeof searchFieldText === 'string' && searchFieldText.length > 0) {
                strSelection = item[searchFieldText];
            }
        }
        let htmlResult = strSelection;
        if(htmlResult==='undefined') {
            return item.text;
        }


        return $('<div>' + htmlResult + '</div>');

    };
    return options;
}
export default function (Alpine) {
    Alpine.directive('select2', (el, { modifiers, expression }, { evaluateLater, effect, cleanup }) => {
        const getSelectData = evaluateLater(expression);

        let instance = null;


        // Observe perubahan dari Alpine ke select2
        effect(() => {
            getSelectData(selectData => {
                // Destroy instance if exists
                if (instance) {
                    $(el).select2('destroy');
                    instance = null;
                }
                const select2Config = buildSelect2Options(selectData);
                if (select2Config.multiple) {
                    el.setAttribute('multiple', 'multiple');
                } else {
                    el.removeAttribute('multiple');
                }

                instance = $(el).select2(select2Config);
                // Sync value to Alpine (via x-model or manual dispatch)
                $(el).on('change', () => {
                    const event = new CustomEvent('input', {
                        bubbles: true,
                        detail: { value: $(el).val() }
                    });
                    el.dispatchEvent(event);
                });
            });
        });

        cleanup(() => {
            if (instance) {
                $(el).select2('destroy');
            }
        });
    });
}
