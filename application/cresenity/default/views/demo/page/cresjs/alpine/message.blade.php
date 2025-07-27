<div x-data="messageData()">
    <button class="btn-primary" @click="$message('Hello world!')">Click me</button>
</div>
@CAppPushScript
<script>
    function messageData() {
        return {

        }
    }
</script>

@CAppEndPushScript
