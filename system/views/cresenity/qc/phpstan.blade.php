<div x-data="phpstanData()" class="capp-testing-container">
    <a href="javascript:;" x-on:click="run()" class="btn btn-primary">Run</a>
    <div class="p-3"></div>
    <div x-show="data!=null">
        <pre x-html="data.result"></pre>
    </div>

</div>
@CAppPushScript
<script>
    function phpstanData() {
        return {
            data: @json($data),
            init() {
                this.checkData();
            },
            async requestPost(url,data = {}) {
                return new Promise((resolve, reject) => {

                    $.ajax({
                        url: url,
                        cache: false,
                        type: 'post',
                        dataType: 'json',
                        contentType: 'application/json',
                        processData: false,
                        data: JSON.stringify(data),
                        success: function(responseData) {
                            //console.log('responseData', responseData);
                            if (typeof responseData.errCode === 'undefined') {
                                cresenity.toast('error','Unknown error');
                                return resolve(false);
                            }
                            if (responseData.errCode != 0) {
                                cresenity.toast('error',responseData.errMessage);
                                return resolve(false);
                            }
                            this.submitted = true;
                            return resolve(responseData.data);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            let error = thrownError;

                            if (typeof thrownError == 'object') {
                                console.log(JSON.stringify(thrownError));
                                error = thrownError.error;
                            }
                            cresenity.toast('error',error);
                            return resolve(false);
                        },
                        complete: function() {


                        },
                    });
                });
            },
            async run() {
                cresenity.blockPage();
                await this.requestPost('{{ $runUrl }}');
                window.location.reload();
                cresenity.unblockPage();
                this.checkData();
            },
            async checkData() {
                const data = await this.requestPost('{{ $pollUrl }}');
                if(data) {
                    this.data = data;
                    if(this.data.isRunning) {
                        setTimeout(() => {
                            this.checkData();
                        }, 500);
                    }
                }
            },
        }
    }
</script>
@CAppEndPushScript
