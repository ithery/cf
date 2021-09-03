<div style="text-align: center">
    <button cf:click="increment">+</button>
    <h1>{{ $count }}</h1>
</div>



<script>
    document.addEventListener('cresenity:load', function () {
        // Get the value of the "count" property
        var someValue = @this.count

        // Set the value of the "count" property
        @this.count = 5

        // Call the increment component action
        @this.increment()

        // Run a callback when an event ("foo") is emitted from this component
        @this.on('foo', () => {})
    })
</script>
