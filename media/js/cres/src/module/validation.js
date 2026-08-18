import strtotime from 'locutus/php/datetime/strtotime';
import strlen from 'locutus/php/strings/strlen';
import is_numeric from 'locutus/php/var/is_numeric';
import array_diff from 'locutus/php/array/array_diff';
import DateFormatter from '../formatter/DateFormatter';
/**
 * @see https://github.com/proengsoft/laravel-jsvalidation
 */
let appValidation = {

    implicitRules: ['Required', 'Confirmed'],

    /**
     * Initialize app validations.
     *
     * @return {void}
     */
    init: function () {
        if ($.validator) {
            // Disable class rules and attribute rules
            $.validator.classRuleSettings = {};
            $.validator.attributeRules = function () {
                this.rules = {};
            };

            $.validator.dataRules = this.arrayRules;
            $.validator.prototype.arrayRulesCache = {};
            // Register validations methods
            this.setupValidations();
        }
    },

    arrayRules: function (element) {
        let rules = {},
            validator = $.data(element.form, 'validator'),
            cache = validator.arrayRulesCache;

        // Is not an Array
        if (element.name.indexOf('[') === -1) {
            return rules;
        }

        if (!(element.name in cache)) {
            cache[element.name] = {};
        }

        $.each(validator.settings.rules, function (name, tmpRules) {
            if (name in cache[element.name]) {
                $.extend(rules, cache[element.name][name]);
            } else {
                cache[element.name][name] = {};
                let nameRegExp = appValidation.helpers.regexFromWildcard(name);
                if (element.name.match(nameRegExp)) {
                    let newRules = $.validator.normalizeRule(tmpRules) || {};
                    cache[element.name][name] = newRules;
                    $.extend(rules, newRules);
                }
            }
        });

        return rules;
    },

    setupValidations: function () {
        /**
         * Create JQueryValidation check to validate CF rules.
         */


        $.validator.addMethod('appValidation', function (value, element, params) {
            let validator = this;
            let validated = true;
            let previous = this.previousValue(element);

            // put Implicit rules in front
            let rules = [];
            $.each(params, function (i, param) {
                if (param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1) {
                    rules.unshift(param);
                } else {
                    rules.push(param);
                }
            });

            $.each(rules, function (i, param) {
                let implicit = param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1;
                let rule = param[0];
                let message = param[2];

                if (!implicit && validator.optional(element)) {
                    validated = 'dependency-mismatch';
                    return false;
                }

                if (appValidation.methods[rule] !== undefined) {
                    validated = appValidation.methods[rule].call(validator, value, element, param[1], function (valid) {
                        validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;
                        if (valid) {
                            let submitted = validator.formSubmitted;
                            validator.prepareElement(element);
                            validator.formSubmitted = submitted;
                            validator.successList.push(element);
                            delete validator.invalid[element.name];
                            validator.showErrors();
                        } else {
                            let errors = {};
                            errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message;
                            validator.invalid[element.name] = true;
                            validator.showErrors(errors);
                        }
                        validator.showErrors(validator.errorMap);
                        previous.valid = valid;
                    });
                } else {
                    validated = false;
                }

                if (validated !== true) {
                    if (!validator.settings.messages[element.name]) {
                        validator.settings.messages[element.name] = {};
                    }
                    validator.settings.messages[element.name].appValidation = message;
                    return false;
                }
            });
            return validated;
        }, '');

        /**
         * Create JQueryValidation check to validate Remote CF rules.
         */
        $.validator.addMethod('appValidationRemote', function (value, element, params) {
            let implicit = false,
                check = params[0][1],
                attribute = element.name,
                token = check[1],
                validateAll = check[2];

            $.each(params, function (i, parameters) {
                implicit = implicit || parameters[3];
            });

            if (!implicit && this.optional(element)) {
                return 'dependency-mismatch';
            }

            let previous = this.previousValue(element),
                validator,
                data;

            if (!this.settings.messages[element.name]) {
                this.settings.messages[element.name] = {};
            }
            previous.originalMessage = this.settings.messages[element.name].appValidationRemote;
            this.settings.messages[element.name].appValidationRemote = previous.message;

            let param = typeof params === 'string' && {
                url: params
            } || params;

            if (appValidation.helpers.arrayEquals(previous.old, value) || previous.old === value) {
                return previous.valid;
            }

            previous.old = value;
            validator = this;
            this.startRequest(element);

            data = $(validator.currentForm).serializeArray();

            data.push({
                name: '_jsvalidation',
                value: attribute
            });

            data.push({
                name: '_jsvalidation_validate_all',
                value: validateAll
            });

            let formMethod = $(validator.currentForm).attr('method');
            if ($(validator.currentForm).find('input[name="_method"]').length) {
                formMethod = $(validator.currentForm).find('input[name="_method"]').val();
            }

            $.ajax($.extend(true, {
                mode: 'abort',
                port: 'validate' + element.name,
                dataType: 'json',
                data: data,
                context: validator.currentForm,
                url: $(validator.currentForm).attr('remote-validation-url'),
                type: formMethod,

                beforeSend: function (xhr) {
                    if ($(validator.currentForm).attr('method').toLowerCase() !== 'get' && token) {
                        return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                    }
                }
            }, param)).always(function (response, textStatus) {
                let errors,
                    message,
                    submitted,
                    valid;

                if (textStatus === 'error') {
                    valid = false;
                    response = appValidation.helpers.parseErrorResponse(response);
                } else if (textStatus === 'success') {
                    valid = response === true || response === 'true';
                } else {
                    return;
                }

                validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;

                if (valid) {
                    submitted = validator.formSubmitted;
                    validator.prepareElement(element);
                    validator.formSubmitted = submitted;
                    validator.successList.push(element);
                    delete validator.invalid[element.name];
                    validator.showErrors();
                } else {
                    errors = {};
                    message = response || validator.defaultMessage(element, 'remote');
                    errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message[0];
                    validator.invalid[element.name] = true;
                    validator.showErrors(errors);
                }
                validator.showErrors(validator.errorMap);
                previous.valid = valid;
                validator.stopRequest(element, valid);
            });
            return 'pending';
        }, '');
    }
};


/*!
 * CApp Javascript Validation
 * Reference https://github.com/proengsoft/laravel-jsvalidation
 * Helper functions used by validators
 *
 */

const initValidation = () => {
    $.extend(true, appValidation, {

        helpers: {

            /**
             * Numeric rules
             */
            numericRules: ['Integer', 'Numeric'],

            fileinfo: function (fieldObj, index) {
                let FileName = fieldObj.value;
                index = typeof index !== 'undefined' ? index : 0;
                if (fieldObj.files !== null) {
                    if (typeof fieldObj.files[index] !== 'undefined') {
                        return {
                            file: FileName,
                            extension: FileName.substr(FileName.lastIndexOf('.') + 1),
                            size: fieldObj.files[index].size / 1024,
                            type: fieldObj.files[index].type
                        };
                    }
                }
                return false;
            },


            selector: function (names) {
                let selector = [];
                if (!$.isArray(names)) {
                    names = [names];
                }
                for (let i = 0; i < names.length; i++) {
                    selector.push('[name=\'' + names[i] + '\']');
                }
                return selector.join();
            },


            hasNumericRules: function (element) {
                return this.hasRules(element, this.numericRules);
            },


            hasRules: function (element, rules) {
                let found = false;
                if (typeof rules === 'string') {
                    rules = [rules];
                }

                let validator = $.data(element.form, 'validator');
                let listRules = [];
                let cache = validator.arrayRulesCache;
                if (element.name in cache) {
                    $.each(cache[element.name], function (index, arrayRule) {
                        listRules.push(arrayRule);
                    });
                }
                if (element.name in validator.settings.rules) {
                    listRules.push(validator.settings.rules[element.name]);
                }
                $.each(listRules, function (index, objRules) {
                    if ('appValidation' in objRules) {
                        let valRules = objRules.appValidation;
                        for (let i = 0; i < valRules.length; i++) {
                            if ($.inArray(valRules[i][0], rules) !== -1) {
                                found = true;
                                return false;
                            }
                        }
                    }
                });

                return found;
            },


            strlen: function (string) {
                return strlen(string);
            },


            getSize: function getSize(obj, element, value) {
                if (this.hasNumericRules(element) && this.is_numeric(value)) {
                    return parseFloat(value);
                } else if ($.isArray(value)) {
                    return parseFloat(value.length);
                } else if (element.type === 'file') {
                    return parseFloat(Math.floor(this.fileinfo(element).size));
                }

                return parseFloat(this.strlen(value));
            },


            getAppValidation: function (rule, element) {
                let found;
                $.each($.validator.staticRules(element), function (key, rules) {
                    if (key === 'appValidation') {
                        $.each(rules, function (i, value) {
                            if (value[0] === rule) {
                                found = value;
                            }
                        });
                    }
                });

                return found;
            },


            parseTime: function (value, format) {
                let timeValue = false;
                let fmt = new DateFormatter();

                if ($.type(format) === 'object') {
                    let dateRule = this.getAppValidation('DateFormat', format);
                    if (dateRule !== undefined) {
                        format = dateRule[1][0];
                    } else {
                        format = null;
                    }
                }

                if (format == null) {
                    timeValue = this.strtotime(value);
                } else {
                    timeValue = fmt.parseDate(value, format);
                    if (timeValue) {
                        timeValue = Math.round((timeValue.getTime() / 1000));
                    }
                }

                return timeValue;
            },


            guessDate: function (value, format) {
                let fmt = new DateFormatter();
                return fmt.guessDate(value, format);
            },


            strtotime: function (text, now) {
                return strtotime(text, now);
            },


            is_numeric: function (mixed_var) {
                return is_numeric(mixed_var);
            },


            arrayDiff: function (arr1, arr2) {
                return array_diff(arr1, arr2);
            },


            arrayEquals: function (arr1, arr2) {
                if (!$.isArray(arr1) || !$.isArray(arr2)) {
                    return false;
                }

                if (arr1.length !== arr2.length) {
                    return false;
                }

                return $.isEmptyObject(this.arrayDiff(arr1, arr2));
            },


            dependentElement: function (validator, element, name) {
                let el = validator.findByName(name);

                if (el[0] !== undefined && validator.settings.onfocusout) {
                    let event = 'blur';
                    if (el[0].tagName === 'SELECT' ||
                        el[0].tagName === 'OPTION' ||
                        el[0].type === 'checkbox' ||
                        el[0].type === 'radio') {
                        event = 'click';
                    }

                    let ruleName = '.validate-appValidation';
                    el.off(ruleName)
                        .off(event + ruleName + '-' + element.name)
                        .on(event + ruleName + '-' + element.name, function () {
                            $(element).valid();
                        });
                }

                return el[0];
            },

            parseErrorResponse: function (response) {
                let newResponse = ['Whoops, looks like something went wrong.'];
                if ('responseText' in response) {
                    let errorMsg = response.responseText.match(/<h1\s*>(.*)<\/h1\s*>/i);
                    if ($.isArray(errorMsg)) {
                        newResponse = [errorMsg[1]];
                    }
                }
                return newResponse;
            },


            escapeRegExp: function (str) {
                return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
            },


            regexFromWildcard: function (name) {
                let nameParts = name.split('[*]');
                if (nameParts.length === 1) {
                    nameParts.push('');
                }
                let regexpParts = nameParts.map(function (currentValue, index) {
                    if (index % 2 === 0) {
                        currentValue = currentValue + '[';
                    } else {
                        currentValue = ']' + currentValue;
                    }

                    return appValidation.helpers.escapeRegExp(currentValue);
                });

                return new RegExp('^' + regexpParts.join('.*') + '$');
            }
        }
    });

    $.extend(true, appValidation, {

        helpers: {


            isTimezone: function (value) {
                let timezones = {
                    africa: [
                        'abidjan',
                        'accra',
                        'addis_ababa',
                        'algiers',
                        'asmara',
                        'bamako',
                        'bangui',
                        'banjul',
                        'bissau',
                        'blantyre',
                        'brazzaville',
                        'bujumbura',
                        'cairo',
                        'casablanca',
                        'ceuta',
                        'conakry',
                        'dakar',
                        'dar_es_salaam',
                        'djibouti',
                        'douala',
                        'el_aaiun',
                        'freetown',
                        'gaborone',
                        'harare',
                        'johannesburg',
                        'juba',
                        'kampala',
                        'khartoum',
                        'kigali',
                        'kinshasa',
                        'lagos',
                        'libreville',
                        'lome',
                        'luanda',
                        'lubumbashi',
                        'lusaka',
                        'malabo',
                        'maputo',
                        'maseru',
                        'mbabane',
                        'mogadishu',
                        'monrovia',
                        'nairobi',
                        'ndjamena',
                        'niamey',
                        'nouakchott',
                        'ouagadougou',
                        'porto-novo',
                        'sao_tome',
                        'tripoli',
                        'tunis',
                        'windhoek'
                    ],
                    america: [
                        'adak',
                        'anchorage',
                        'anguilla',
                        'antigua',
                        'araguaina',
                        'argentina\/buenos_aires',
                        'argentina\/catamarca',
                        'argentina\/cordoba',
                        'argentina\/jujuy',
                        'argentina\/la_rioja',
                        'argentina\/mendoza',
                        'argentina\/rio_gallegos',
                        'argentina\/salta',
                        'argentina\/san_juan',
                        'argentina\/san_luis',
                        'argentina\/tucuman',
                        'argentina\/ushuaia',
                        'aruba',
                        'asuncion',
                        'atikokan',
                        'bahia',
                        'bahia_banderas',
                        'barbados',
                        'belem',
                        'belize',
                        'blanc-sablon',
                        'boa_vista',
                        'bogota',
                        'boise',
                        'cambridge_bay',
                        'campo_grande',
                        'cancun',
                        'caracas',
                        'cayenne',
                        'cayman',
                        'chicago',
                        'chihuahua',
                        'costa_rica',
                        'creston',
                        'cuiaba',
                        'curacao',
                        'danmarkshavn',
                        'dawson',
                        'dawson_creek',
                        'denver',
                        'detroit',
                        'dominica',
                        'edmonton',
                        'eirunepe',
                        'el_salvador',
                        'fortaleza',
                        'glace_bay',
                        'godthab',
                        'goose_bay',
                        'grand_turk',
                        'grenada',
                        'guadeloupe',
                        'guatemala',
                        'guayaquil',
                        'guyana',
                        'halifax',
                        'havana',
                        'hermosillo',
                        'indiana\/indianapolis',
                        'indiana\/knox',
                        'indiana\/marengo',
                        'indiana\/petersburg',
                        'indiana\/tell_city',
                        'indiana\/vevay',
                        'indiana\/vincennes',
                        'indiana\/winamac',
                        'inuvik',
                        'iqaluit',
                        'jamaica',
                        'juneau',
                        'kentucky\/louisville',
                        'kentucky\/monticello',
                        'kralendijk',
                        'la_paz',
                        'lima',
                        'los_angeles',
                        'lower_princes',
                        'maceio',
                        'managua',
                        'manaus',
                        'marigot',
                        'martinique',
                        'matamoros',
                        'mazatlan',
                        'menominee',
                        'merida',
                        'metlakatla',
                        'mexico_city',
                        'miquelon',
                        'moncton',
                        'monterrey',
                        'montevideo',
                        'montreal',
                        'montserrat',
                        'nassau',
                        'new_york',
                        'nipigon',
                        'nome',
                        'noronha',
                        'north_dakota\/beulah',
                        'north_dakota\/center',
                        'north_dakota\/new_salem',
                        'ojinaga',
                        'panama',
                        'pangnirtung',
                        'paramaribo',
                        'phoenix',
                        'port-au-prince',
                        'port_of_spain',
                        'porto_velho',
                        'puerto_rico',
                        'rainy_river',
                        'rankin_inlet',
                        'recife',
                        'regina',
                        'resolute',
                        'rio_branco',
                        'santa_isabel',
                        'santarem',
                        'santiago',
                        'santo_domingo',
                        'sao_paulo',
                        'scoresbysund',
                        'shiprock',
                        'sitka',
                        'st_barthelemy',
                        'st_johns',
                        'st_kitts',
                        'st_lucia',
                        'st_thomas',
                        'st_vincent',
                        'swift_current',
                        'tegucigalpa',
                        'thule',
                        'thunder_bay',
                        'tijuana',
                        'toronto',
                        'tortola',
                        'vancouver',
                        'whitehorse',
                        'winnipeg',
                        'yakutat',
                        'yellowknife'
                    ],
                    antarctica: [
                        'casey',
                        'davis',
                        'dumontdurville',
                        'macquarie',
                        'mawson',
                        'mcmurdo',
                        'palmer',
                        'rothera',
                        'south_pole',
                        'syowa',
                        'vostok'
                    ],
                    arctic: [
                        'longyearbyen'
                    ],
                    asia: [
                        'aden',
                        'almaty',
                        'amman',
                        'anadyr',
                        'aqtau',
                        'aqtobe',
                        'ashgabat',
                        'baghdad',
                        'bahrain',
                        'baku',
                        'bangkok',
                        'beirut',
                        'bishkek',
                        'brunei',
                        'choibalsan',
                        'chongqing',
                        'colombo',
                        'damascus',
                        'dhaka',
                        'dili',
                        'dubai',
                        'dushanbe',
                        'gaza',
                        'harbin',
                        'hebron',
                        'ho_chi_minh',
                        'hong_kong',
                        'hovd',
                        'irkutsk',
                        'jakarta',
                        'jayapura',
                        'jerusalem',
                        'kabul',
                        'kamchatka',
                        'karachi',
                        'kashgar',
                        'kathmandu',
                        'khandyga',
                        'kolkata',
                        'krasnoyarsk',
                        'kuala_lumpur',
                        'kuching',
                        'kuwait',
                        'macau',
                        'magadan',
                        'makassar',
                        'manila',
                        'muscat',
                        'nicosia',
                        'novokuznetsk',
                        'novosibirsk',
                        'omsk',
                        'oral',
                        'phnom_penh',
                        'pontianak',
                        'pyongyang',
                        'qatar',
                        'qyzylorda',
                        'rangoon',
                        'riyadh',
                        'sakhalin',
                        'samarkand',
                        'seoul',
                        'shanghai',
                        'singapore',
                        'taipei',
                        'tashkent',
                        'tbilisi',
                        'tehran',
                        'thimphu',
                        'tokyo',
                        'ulaanbaatar',
                        'urumqi',
                        'ust-nera',
                        'vientiane',
                        'vladivostok',
                        'yakutsk',
                        'yekaterinburg',
                        'yerevan'
                    ],
                    atlantic: [
                        'azores',
                        'bermuda',
                        'canary',
                        'cape_verde',
                        'faroe',
                        'madeira',
                        'reykjavik',
                        'south_georgia',
                        'st_helena',
                        'stanley'
                    ],
                    australia: [
                        'adelaide',
                        'brisbane',
                        'broken_hill',
                        'currie',
                        'darwin',
                        'eucla',
                        'hobart',
                        'lindeman',
                        'lord_howe',
                        'melbourne',
                        'perth',
                        'sydney'
                    ],
                    europe: [
                        'amsterdam',
                        'andorra',
                        'athens',
                        'belgrade',
                        'berlin',
                        'bratislava',
                        'brussels',
                        'bucharest',
                        'budapest',
                        'busingen',
                        'chisinau',
                        'copenhagen',
                        'dublin',
                        'gibraltar',
                        'guernsey',
                        'helsinki',
                        'isle_of_man',
                        'istanbul',
                        'jersey',
                        'kaliningrad',
                        'kiev',
                        'lisbon',
                        'ljubljana',
                        'london',
                        'luxembourg',
                        'madrid',
                        'malta',
                        'mariehamn',
                        'minsk',
                        'monaco',
                        'moscow',
                        'oslo',
                        'paris',
                        'podgorica',
                        'prague',
                        'riga',
                        'rome',
                        'samara',
                        'san_marino',
                        'sarajevo',
                        'simferopol',
                        'skopje',
                        'sofia',
                        'stockholm',
                        'tallinn',
                        'tirane',
                        'uzhgorod',
                        'vaduz',
                        'vatican',
                        'vienna',
                        'vilnius',
                        'volgograd',
                        'warsaw',
                        'zagreb',
                        'zaporozhye',
                        'zurich'
                    ],
                    indian: [
                        'antananarivo',
                        'chagos',
                        'christmas',
                        'cocos',
                        'comoro',
                        'kerguelen',
                        'mahe',
                        'maldives',
                        'mauritius',
                        'mayotte',
                        'reunion'
                    ],
                    pacific: [
                        'apia',
                        'auckland',
                        'chatham',
                        'chuuk',
                        'easter',
                        'efate',
                        'enderbury',
                        'fakaofo',
                        'fiji',
                        'funafuti',
                        'galapagos',
                        'gambier',
                        'guadalcanal',
                        'guam',
                        'honolulu',
                        'johnston',
                        'kiritimati',
                        'kosrae',
                        'kwajalein',
                        'majuro',
                        'marquesas',
                        'midway',
                        'nauru',
                        'niue',
                        'norfolk',
                        'noumea',
                        'pago_pago',
                        'palau',
                        'pitcairn',
                        'pohnpei',
                        'port_moresby',
                        'rarotonga',
                        'saipan',
                        'tahiti',
                        'tarawa',
                        'tongatapu',
                        'wake',
                        'wallis'
                    ],
                    utc: [
                        ''
                    ]
                };

                let tzparts = value.split('/', 2);
                let continent = tzparts[0].toLowerCase();
                let city = '';
                if (tzparts[1]) {
                    city = tzparts[1].toLowerCase();
                }

                return (continent in timezones && (timezones[continent].length === 0 || timezones[continent].indexOf(city) !== -1));
            }
        }
    });

    /*!
     * Methods that implement CApp Validations
     */

    $.extend(true, appValidation, {

        methods: {

            helpers: appValidation.helpers,
            jsRemoteTimer: 0,

            Sometimes: function () {
                return true;
            },

            Bail: function () {
                return true;
            },

            Nullable: function () {
                return true;
            },

            Filled: function (value, element) {
                return $.validator.methods.required.call(this, value, element, true);
            },

            Required: function (value, element) {
                return $.validator.methods.required.call(this, value, element);
            },

            RequiredWith: function (value, element, params) {
                let validator = this,
                    required = false;
                let currentObject = this;
                $.each(params, function (i, param) {
                    let target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                    required = required || (
                        target !== undefined &&
                        $.validator.methods.required.call(
                            validator,
                            currentObject.elementValue(target),
                            target, true));
                });
                if (required) {
                    return $.validator.methods.required.call(this, value, element, true);
                }
                return true;
            },

            RequiredWithAll: function (value, element, params) {
                let validator = this,
                    required = true;
                let currentObject = this;
                $.each(params, function (i, param) {
                    let target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                    required = required && (
                        target !== undefined &&
                        $.validator.methods.required.call(
                            validator,
                            currentObject.elementValue(target),
                            target, true));
                });
                if (required) {
                    return $.validator.methods.required.call(this, value, element, true);
                }
                return true;
            },

            RequiredWithout: function (value, element, params) {
                let validator = this,
                    required = false;
                let currentObject = this;
                $.each(params, function (i, param) {
                    let target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                    required = required ||
                        target === undefined ||
                        !$.validator.methods.required.call(
                            validator,
                            currentObject.elementValue(target),
                            target, true);
                });
                if (required) {
                    return $.validator.methods.required.call(this, value, element, true);
                }
                return true;
            },

            RequiredWithoutAll: function (value, element, params) {
                let validator = this,
                    required = true,
                    currentObject = this;
                $.each(params, function (i, param) {
                    let target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                    required = required && (
                        target === undefined ||
                        !$.validator.methods.required.call(
                            validator,
                            currentObject.elementValue(target),
                            target, true));
                });
                if (required) {
                    return $.validator.methods.required.call(this, value, element, true);
                }
                return true;
            },

            RequiredIf: function (value, element, params) {
                let target = appValidation.helpers.dependentElement(
                    this, element, params[0]);
                if (target !== undefined) {
                    let val = String(this.elementValue(target));
                    if (typeof val !== 'undefined') {
                        let data = params.slice(1);
                        if ($.inArray(val, data) !== -1) {
                            return $.validator.methods.required.call(
                                this, value, element, true);
                        }
                    }
                }

                return true;
            },

            RequiredUnless: function (value, element, params) {
                let target = appValidation.helpers.dependentElement(
                    this, element, params[0]);
                if (target !== undefined) {
                    let val = String(this.elementValue(target));
                    if (typeof val !== 'undefined') {
                        let data = params.slice(1);
                        if ($.inArray(val, data) !== -1) {
                            return true;
                        }
                    }
                }

                return $.validator.methods.required.call(
                    this, value, element, true);
            },

            Confirmed: function (value, element, params) {
                return appValidation.methods.Same.call(this, value, element, params);
            },

            Same: function (value, element, params) {
                let target = appValidation.helpers.dependentElement(
                    this, element, params[0]);
                if (target !== undefined) {
                    return String(value) === String(this.elementValue(target));
                }
                return false;
            },

            InArray: function (value, element, params) {
                if (typeof params[0] === 'undefined') {
                    return false;
                }
                let elements = this.elements();
                let found = false;
                let nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);
                for (let i = 0; i < elements.length; i++) {
                    let targetName = elements[i].name;
                    if (targetName.match(nameRegExp)) {
                        let equals = appValidation.methods.Same.call(this, value, element, [targetName]);
                        found = found || equals;
                    }
                }

                return found;
            },

            Distinct: function (value, element, params) {
                if (typeof params[0] === 'undefined') {
                    return false;
                }

                let elements = this.elements();
                let found = false;
                let nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);
                for (let i = 0; i < elements.length; i++) {
                    let targetName = elements[i].name;
                    if (targetName !== element.name && targetName.match(nameRegExp)) {
                        let equals = appValidation.methods.Same.call(this, value, element, [targetName]);
                        found = found || equals;
                    }
                }

                return !found;
            },

            Different: function (value, element, params) {
                return !appValidation.methods.Same.call(this, value, element, params);
            },

            Accepted: function (value) {
                let regex = new RegExp('^(?:(yes|on|1|true))$', 'i');
                return regex.test(value);
            },

            Array: function (value, element) {
                if (element.name.indexOf('[') !== -1 && element.name.indexOf(']') !== -1) {
                    return true;
                }

                return $.isArray(value);
            },

            Boolean: function (value) {
                let regex = new RegExp('^(?:(true|false|1|0))$', 'i');
                return regex.test(value);
            },

            Integer: function (value) {
                let regex = new RegExp('^(?:-?\\d+)$', 'i');
                return regex.test(value);
            },

            Numeric: function (value, element) {
                return $.validator.methods.number.call(this, value, element, true);
            },

            String: function (value) {
                return typeof value === 'string';
            },

            Digits: function (value, element, params) {
                return (
                    $.validator.methods.number.call(this, value, element, true) &&
                    value.length === parseInt(params, 10));
            },

            DigitsBetween: function (value, element, params) {
                return ($.validator.methods.number.call(this, value, element, true)
                    && value.length >= parseFloat(params[0]) && value.length <= parseFloat(params[1]));
            },

            Size: function (value, element, params) {
                return appValidation.helpers.getSize(this, element, value) === parseFloat(params[0]);
            },

            Between: function (value, element, params) {
                return (appValidation.helpers.getSize(this, element, value) >= parseFloat(params[0]) &&
                    appValidation.helpers.getSize(this, element, value) <= parseFloat(params[1]));
            },

            Min: function (value, element, params) {
                return appValidation.helpers.getSize(this, element, value) >= parseFloat(params[0]);
            },

            Max: function (value, element, params) {
                return appValidation.helpers.getSize(this, element, value) <= parseFloat(params[0]);
            },

            In: function (value, element, params) {
                if ($.isArray(value) && appValidation.helpers.hasRules(element, 'Array')) {
                    let diff = appValidation.helpers.arrayDiff(value, params);
                    return Object.keys(diff).length === 0;
                }
                return params.indexOf(value.toString()) !== -1;
            },

            NotIn: function (value, element, params) {
                return params.indexOf(value.toString()) === -1;
            },

            Ip: function (value) {
                return /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(value) ||
                    /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
            },

            Email: function (value, element) {
                return $.validator.methods.email.call(this, value, element, true);
            },

            Url: function (value, element) {
                return $.validator.methods.url.call(this, value, element, true);
            },

            File: function (value, element) {
                if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                    return true;
                }
                if ('files' in element) {
                    return (element.files.length > 0);
                }
                return false;
            },

            Mimes: function (value, element, params) {
                if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                    return true;
                }
                let lowerParams = $.map(params, function (item) {
                    return item.toLowerCase();
                });
                let fileinfo = appValidation.helpers.fileinfo(element);
                return (fileinfo !== false && lowerParams.indexOf(fileinfo.extension.toLowerCase()) !== -1);
            },

            Mimetypes: function (value, element, params) {
                if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                    return true;
                }
                let lowerParams = $.map(params, function (item) {
                    return item.toLowerCase();
                });
                let fileinfo = appValidation.helpers.fileinfo(element);
                if (fileinfo === false) {
                    return false;
                }
                return (lowerParams.indexOf(fileinfo.type.toLowerCase()) !== -1);
            },

            Image: function (value, element) {
                return appValidation.methods.Mimes.call(this, value, element, [
                    'jpg', 'png', 'gif', 'bmp', 'svg', 'jpeg'
                ]);
            },

            Dimensions: function (value, element, params, callback) {
                if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                    return true;
                }
                if (element.files === null || typeof element.files[0] === 'undefined') {
                    return false;
                }

                let fr = new FileReader();
                fr.onload = function () {
                    let img = new Image();
                    img.onload = function () {
                        let height = parseFloat(img.naturalHeight);
                        let width = parseFloat(img.naturalWidth);
                        let ratio = width / height;
                        let notValid = ((params.width) && parseFloat(params.width !== width)) ||
                            ((params.min_width) && parseFloat(params.min_width) > width) ||
                            ((params.max_width) && parseFloat(params.max_width) < width) ||
                            ((params.height) && parseFloat(params.height) !== height) ||
                            ((params.min_height) && parseFloat(params.min_height) > height) ||
                            ((params.max_height) && parseFloat(params.max_height) < height) ||
                            ((params.ratio) && ratio !== parseFloat(eval(params.ratio)));
                        callback(!notValid);
                    };
                    img.onerror = function () {
                        callback(false);
                    };
                    img.src = fr.result;
                };
                fr.readAsDataURL(element.files[0]);
                return 'pending';
            },

            Alpha: function (value) {
                if (typeof value !== 'string') {
                    return false;
                }

                let regex = new RegExp('^(?:^[a-z\u00E0-\u00FC]+$)$', 'i');
                return regex.test(value);
            },

            AlphaNum: function (value) {
                if (typeof value !== 'string') {
                    return false;
                }
                let regex = new RegExp('^(?:^[a-z0-9\u00E0-\u00FC]+$)$', 'i');
                return regex.test(value);
            },

            AlphaDash: function (value) {
                if (typeof value !== 'string') {
                    return false;
                }
                let regex = new RegExp('^(?:^[a-z0-9\u00E0-\u00FC_-]+$)$', 'i');
                return regex.test(value);
            },

            Regex: function (value, element, params) {
                let invalidModifiers = ['x', 's', 'u', 'X', 'U', 'A'];
                // Converting php regular expression
                let phpReg = new RegExp('^(?:\/)(.*\\\/?[^\/]*|[^\/]*)(?:\/)([gmixXsuUAJ]*)?$');
                let matches = params[0].match(phpReg);
                if (matches === null) {
                    return false;
                }
                // checking modifiers
                let php_modifiers = [];
                if (matches[2] !== undefined) {
                    php_modifiers = matches[2].split('');
                    for (let i = 0; i < php_modifiers.length < i; i++) {
                        if (invalidModifiers.indexOf(php_modifiers[i]) !== -1) {
                            return true;
                        }
                    }
                }
                let regex = new RegExp('^(?:' + matches[1] + ')$', php_modifiers.join());
                return regex.test(value);
            },

            Date: function (value) {
                return (appValidation.helpers.strtotime(value) !== false);
            },

            DateFormat: function (value, element, params) {
                return appValidation.helpers.parseTime(value, params[0]) !== false;
            },

            Before: function (value, element, params) {
                let timeCompare = parseFloat(params);
                if (isNaN(timeCompare)) {
                    let target = appValidation.helpers.dependentElement(this, element, params);
                    if (target === undefined) {
                        return false;
                    }
                    timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
                }

                let timeValue = appValidation.helpers.parseTime(value, element);
                return (timeValue !== false && timeValue < timeCompare);
            },

            After: function (value, element, params) {
                let timeCompare = parseFloat(params);
                if (isNaN(timeCompare)) {
                    let target = appValidation.helpers.dependentElement(this, element, params);
                    if (target === undefined) {
                        return false;
                    }
                    timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
                }

                let timeValue = appValidation.helpers.parseTime(value, element);
                return (timeValue !== false && timeValue > timeCompare);
            },

            Timezone: function (value) {
                return appValidation.helpers.isTimezone(value);
            },

            Json: function (value) {
                let result = true;
                try {
                    JSON.parse(value);
                } catch (e) {
                    result = false;
                }
                return result;
            }
        }
    });
    appValidation.init();
};

export default initValidation;
