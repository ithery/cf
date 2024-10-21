<div>
    <a id="btn-click" class="btn btn-primary">Click Me</a>
</div>
<div id="reactive-content"></div>
@CAppPushScript
<script>
    window.addEventListener('cresenity:loaded', () => {
        let data = {
            click: 0,
        }
        reactiveData = cresenity.reactive(data, function(data) {
            document.getElementById('reactive-content').innerHTML = 'clicked ' + data.click;
        });
        document.getElementById('btn-click').addEventListener('click', ()=>{
            reactiveData.click = reactiveData.click + 1;
        })
    });
</script>

@CAppEndPushScript
