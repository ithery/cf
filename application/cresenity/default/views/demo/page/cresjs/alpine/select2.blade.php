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
</div>

@CAppPushScript
<script>

function select2Data() {
    return {
        selectedOption:null,
        init() {
            $('#my-select2-input').select2().on('change',()=>{
                this.updateSelection();
            });

            this.$nextTick(()=>{
                this.updateSelection();
            })

        },
        updateSelection() {
            this.selectedOption = $('#my-select2-input').select2('val');
        }
    }
}

</script>
@CAppEndPushScript
