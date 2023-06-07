
@CAppPushScript
<script>
    window.addEventListener('cresenity:loaded', ()=>{
        cresenity.setConfirmHandler((owner, options, next) => {
            if(confirm(options.message)) {
                console.log('OK');
                next(true);

            } else {
                console.log('NOT OK');
                next(false);
            }

        });

    });
</script>

@CAppEndPushScript
