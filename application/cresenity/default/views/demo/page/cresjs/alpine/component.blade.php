<style>

</style>
<div x-data="componentData()" >
    <cres-greeting greeting-word="Hi" :who="name"></cres-greeting>

    <template x-component="greeting">
        <h1><span x-text="$prop('greeting-word')"></span> <span x-text="$prop('who')"></span></h1>
        <button @click="$api.say()">Click me</button>
        <script>
            return {
                say() {
                    alert(this.$prop('greeting-word') + ' ' + this.$prop('who'))
                }
            }
        </script>
    </template>
</div>

@CAppPushScript
<script>
    function componentData() {
        return {
            name: 'John Doe'
        }
    }
</script>
@CAppEndPushScript
