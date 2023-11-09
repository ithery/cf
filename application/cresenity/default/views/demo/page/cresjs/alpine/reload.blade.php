<style>

</style>
<div x-data="controlData()" class="accordion">
    <h5>Auto Numeric</h5>

    <input type="text" x-model="value"/>

    <div class="my-3" x-reload="'{{ c::url('demo/cresjs/alpine/reload/reload') }}' + '/' + value"></div>

</div>
@CAppPushScript
<script>
    function controlData() {
        return {
            value:'hello world'
        }
    }
</script>

@CAppEndPushScript
