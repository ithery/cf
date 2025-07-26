<div x-data="select2Data()">
    @CAppElement(function() {
        $selectSearch = new CElement_FormInput_SelectSearch('my-select2-input');
        $selectSearch->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('country_id');
        $selectSearch->setSearchField('name');
        $selectSearch->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        $selectSearch->setAutoSelect();
        return $selectSearch;
    })


    <p class="mt-3">Selected value: <span x-text="selectedOption"></span></p>
    <hr/>
    <select name="my-select2-input-2" id="my-select2-input-2"></select>
</div>

@CAppPushScript
<script>

function select2Data() {
    return {
        selectedOption:null,
        selectData: @json($selectData),
        init() {
            $('#my-select2-input').select2().on('change',()=>{
                this.updateSelection();
            });

            this.$nextTick(()=>{
                this.updateSelection();
            })
            this.initSelect2();
        },
        updateSelection() {
            this.selectedOption = $('#my-select2-input').select2('val');
        },
        initSelect2(){
            const select2Options = this.buildSelect2Options();
            console.log(select2Options);
            $('#my-select2-input-2').select2(select2Options);
        },
        buildSelect2Options() {
            let options = {};
            options.width='100%';
            options.language = this.selectData.language;
            options.allowClear = this.selectData.allowClear ? 'true' : 'false';
            options.placeholder = this.selectData.placeholder;
            let ajaxOptions = {};
            ajaxOptions.url = this.selectData.ajaxUrl;
            ajaxOptions.dataType = 'jsonp';
            ajaxOptions.quietMillis = this.selectData.delay;
            ajaxOptions.delay = this.selectData.delay;
            ajaxOptions.multiple = this.selectData.multiple;
            ajaxOptions.data =  (params) => {
                let result = {
                    q: params.term, // search term
                    page: params.page,
                    limit: this.selectData.perPage
                };
                return result;
            },

            ajaxOptions.processResults = (data, params) => {
                console.log('select2.processResults', data, params);
                params.page = params.page || 1;
                var more = (params.page * this.selectData.perPage) < data.total;
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
            console.log(this.selectData.searchField);
            if(this.selectData.selectedData) {
                let selectedData = this.selectData.selectedData;
                if (Array.isArray(selectedData) && selectedData.length > 0) {
                    if (this.selectData.multiple == false) {
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
                console.log('templateResult',item);
                if(item.cappFormatResult) {
                    if(item.cappFormatResultIsHtml) {
                        return $('<div>' + item.cappFormatResult +'</div>');
                    } else {
                        return item.cappFormatResult;
                    }
                }

                return $('<div>' + this.selectData.formatResult + '</div>');
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
                console.log('templateSelection2',item);
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
                let strSelection = this.selectData.formatSelection;

                if (!strSelection) {
                    let searchFieldText = this.selectData.searchField?.[0];

                    if (typeof searchFieldText === 'string' && searchFieldText.length > 0) {
                        strSelection = item[searchFieldText];
                    }
                }
                let htmlResult = strSelection;
                console.log('templateSelection',htmlResult);
                if(htmlResult==='undefined') {
                    return item.text;
                }


                return $('<div>' + htmlResult + '</div>');

            };
            return options;
        }

    }
}

</script>
@CAppEndPushScript
