import { debounce } from '../../../util/debounce';

export default class SelectTwo {
    constructor(element) {
        this.element = element;
        this.config = JSON.parse(element.getAttribute('cres-config') || '{}');
        this.init();
    }

    init() {
        const $el = $(this.element);

        if ($el.data('select2')) {
            $el.select2('destroy');
        }

        const config = this.config;
        const dependsOn = config.dependsOn || [];
        const options = {
            width: '100%',
            language: config.language || 'en',
            placeholder: config.placeholder || '',
            allowClear: config.allowClear || false,
            minimumInputLength: config.minInputLength || 0
        };

        const modal = $el.closest('.modal');
        if (modal.length > 0) {
            options.dropdownParent = modal;
        }

        if (config.multiple) {
            options.multiple = true;
        }

        if (config.ajaxUrl) {
            options.ajax = {
                url: config.ajaxUrl,
                dataType: 'jsonp',
                delay: config.delay || 100,
                data: function (params) {
                    let result = {
                        q: params.term,
                        page: params.page,
                        limit: config.perPage || 10
                    };
                    for (let i = 0; i < dependsOn.length; i++) {
                        let dep = dependsOn[i];
                        let val = SelectTwo.getValueFromSelectors(dep.selectors);
                        result[dep.key] = val;
                    }
                    return result;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    const more = (params.page * (config.perPage || 10)) < data.total;
                    return {
                        results: data.data,
                        pagination: {more: more}
                    };
                },
                cache: true,
                error: function (jqXHR, status, error) {
                    if (window.cresenity && window.cresenity.handleAjaxError) {
                        window.cresenity.handleAjaxError(jqXHR, status, error);
                    }
                }
            };
        }

        const templateFn = config.format
            ? this.buildTemplateFn(config.format)
            : this.buildTemplateFn('{' + (config.searchField || 'text') + '}');

        options.templateResult = function (item) {
            if (item.loading) {
                return item.text;
            }
            if (item.cappFormatResult) {
                return item.cappFormatResultIsHtml
                    ? $('<div>' + item.cappFormatResult + '</div>')
                    : item.cappFormatResult;
            }
            return templateFn(item);
        };

        options.templateSelection = function (item) {
            if (item.element) {
                let dataContent = $(item.element).attr('data-content');
                if (dataContent) {
                    return (/<\/?[a-z][\s\S]*>/i).test(dataContent) ? $(dataContent) : dataContent;
                }
            }
            if (item.cappFormatSelection) {
                return item.cappFormatSelectionIsHtml
                    ? $('<div>' + item.cappFormatSelection + '</div>')
                    : item.cappFormatSelection;
            }
            if (item.id === '' && item.text) {
                return item.text;
            }
            return templateFn(item);
        };

        $el.select2(options);

        $el.on('select2:open', function () {
            const parentModal = $el.closest('.modal');
            if (parentModal.length > 0) {
                const modalZ = parseInt(parentModal.css('z-index'), 10) || 1050;
                const dropdown = $el.data('select2').$dropdown;
                if (dropdown) {
                    dropdown.css('z-index', modalZ + 1);
                }
            }
        });

        if (dependsOn.length > 0) {
            this.initDependsOn($el, dependsOn);
        }

        this.element.setAttribute('data-cres-initialized', '1');
    }

    initDependsOn($el, dependsOn) {
        let self = this;
        for (let i = 0; i < dependsOn.length; i++) {
            let dep = dependsOn[i];
            let selectorStr = dep.selectors.join(', ');
            let block = dep.block !== false;
            $(selectorStr).on('change', debounce(function () {
                let $container = $el.closest('.form-group').length > 0 ? $el.closest('.form-group') : $el.parent();
                if (block) {
                    window.cresenity.blockElement($container);
                }
                $el.val(null).trigger('change');
                if ($el.data('select2')) {
                    $el.select2('destroy');
                }
                $el.empty();
                self.element.removeAttribute('data-cres-initialized');
                self.element.classList.remove('cres:initialized');
                self.init();
                if (block) {
                    window.cresenity.unblockElement($container);
                }
            }, dep.throttle || 100));
        }
    }

    static getValueFromSelectors(selectors) {
        if (selectors.length === 1) {
            let $s = $(selectors[0]);
            return $s.is(':checkbox') ? $(selectors[0] + ':checked').val() : $s.val();
        }
        let values = [];
        for (let i = 0; i < selectors.length; i++) {
            let $s = $(selectors[i]);
            values.push($s.is(':checkbox') ? $(selectors[i] + ':checked').val() : $s.val());
        }
        return values;
    }

    buildTemplateFn(format) {
        return function (item) {
            if (!item.id && item.text) {
                return item.text;
            }
            let html = format;
            html = html.replace(/\{([\w.]+)\}/g, function (match, key) {
                return item[key] !== undefined ? item[key] : '';
            });
            return $('<div>' + html + '</div>');
        };
    }
}
