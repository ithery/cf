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
        }
    }
}

</script>
@CAppEndPushScript
