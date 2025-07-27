<div x-data="select2Data()">
    @CAppElement(function() {
        $selectSearch = new CElement_FormInput_SelectSearch('my-select2-input');
        $selectSearch->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('id');
        $selectSearch->setSearchField('name');
        $selectSearch->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        // $selectSearch->setAutoSelect();
        $selectSearch->setMultiple();
        $selectSearch->setValue([1,2]);
        return $selectSearch;
    })


    <p class="mt-3">Selected value: <span x-text="selectedOption"></span></p>
    <hr/>
    <h3>Select 2 With Option</h3>
    <select name="my-select2-input-2" id="my-select2-input-2"></select>
    <p class="mt-3">Selected value 2: <span x-text="selectedOption2"></span></p>
    <h3>Select 2 With x-select</h3>
    <select name="my-select2-input-3" id="my-select2-input-3" x-select2="selectData"></select>
    <p class="mt-3">Selected value 3: <span x-text="selectedOption3"></span></p>
</div>

@CAppPushScript
<script>

function select2Data() {
    return {
        selectedOption:null,
        selectedOption2:null,
        selectedOption3:null,
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
            this.selectedOption2 = $('#my-select2-input-2').select2('val');
            this.selectedOption3 = $('#my-select2-input-3').select2('val');

        },
        initSelect2(){
            const select2Options = this.buildSelect2Options();
            if(select2Options.multiple) {
                $('#my-select2-input-2').attr('multiple','multiple');
            }
            const selectedData = this.selectData.selectedData;
            if(selectedData) {
                for(const selectedValue of selectedData) {

                }
            }
            // $html->appendln('<option data-multiple="' . ($this->multiple ? '1' : '0') . '" value="' . $selectedValue . '" data-content="' . c::e($strSelection) . '" selected="selected" >' . $strSelection . '</option>');
            $('#my-select2-input-2').select2(select2Options);
        },
        buildSelect2Options() {
            let options = {};
            options.width='100%';
            options.language = this.selectData.language;
            options.allowClear = this.selectData.allowClear ? 'true' : 'false';
            options.placeholder = this.selectData.placeholder;
            options.multiple = this.selectData.multiple;
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
            let selectedData = this.selectData.selectedData;

            if(selectedData) {
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
