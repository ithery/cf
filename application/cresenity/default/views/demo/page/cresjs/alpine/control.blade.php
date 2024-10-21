<style>

</style>
<div x-data="controlData()" class="accordion">
    <h5>Auto Numeric</h5>

    <input type="text" x-autonumeric x-model="autoNumericValue"/>

    <div class="my-3">Auto Numeric Value : <span x-text="autoNumericValue"></span></div>

</div>
@CAppPushScript
<script>
    function controlData() {
        return {
            autoNumericValue:@json($autoNumericValue)
        }
    }
</script>

@CAppEndPushScript
