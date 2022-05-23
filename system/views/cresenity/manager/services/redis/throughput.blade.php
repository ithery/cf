<style>
    table.redis-throughput code {
      color: #5e6d82;
      background-color: #e6effb;
      margin: 0 4px;
      display: inline-block;
      padding: 1px 5px;
      font-size: 12px;
      border-radius: 3px;
      height: 18px;
      line-height: 18px;
    }
    table.redis-throughput {
      width: 100%;
    }
    table.redis-throughput td.text {
      font-family: monospace;
    }
    table.redis-throughput td {
      border-bottom: 1px solid #e6ebf5;
      padding: 5px 0;
      min-width: 0;
      box-sizing: border-box;
      text-overflow: ellipsis;
      vertical-align: middle;
      position: relative;
      border-collapse: separate;
    }
    </style>
<div x-data="redisThroughput()">
    <table class="redis-throughput">
        <thead>
            <tr>
                <td class="text">Command</td>
                <td class="text">calls</td>
                <td class="text">usec</td>
                <td class="text">usec_per_call</td>
            </tr>
        </thead>

        <tbody>
            <template x-for="(info, command) in commands" x-key="command">
                <tr>
                    <td class="text" x-text="command"></el-tag></td>
                    <td><code x-text="info.calls"></code></td>
                    <td><code x-text="info.usec"></code></td>
                    <td><code x-text="info.usec_per_call"></code></td>
                </tr>
            </template>
        </tbody>
    </table>
</div>


@CAppPushScript
<script>

    window.redisThroughput = function() {

        return {
            commands : null,
            interval: null,
            chart:null,
            pollingUrl: '{{ $pollingUrl }}',
            init() {
                if(window.cresenity) {
                    this.fillData();
                    this.refreshPeriodically();
                    cresenity.on('reload:success',()=> {
                        console.log('reloadSuccess');
                        this.destroy()
                    });
                } else {
                    window.addEventListener('cresenity:loaded',()=>{
                        this.fillData();
                        this.refreshPeriodically();
                        cresenity.on('reload:success',()=> this.destroy());
                    });
                }
            },

            refreshPeriodically() {
                this.interval = setInterval(() => {
                    this.fillData();
                }, 3000);
            },
            destroy() {
                clearInterval(this.interval);
            },
            time () {
                const d = new Date();
                return d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
            },
            async fillData () {
                let response = await this.getData();
                this.commands = response;
            },
            async getData() {
                return new Promise((resolve, reject) => {

                    $.ajax({
                        url: this.pollingUrl,
                        cache: false,
                        type: 'post',
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function (responseData) {
                            console.log('responseData',responseData);
                            if(typeof responseData.errCode === 'undefined') {
                                feWeb.showError('Unknown error');
                                return resolve(false);
                            }
                            if(responseData.errCode != 0) {
                                cresenity.toast({
                                    type:error,
                                    message:responseData.errMessage,

                                });
                                return resolve(false);
                            }

                            return resolve(responseData.data);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            let error = thrownError;

                            if(typeof thrownError == 'object') {
                                console.log(JSON.stringify(thrownError));
                                error = thrownError.error;
                            }
                            cresenity.toast({
                                type:error,
                                message:error,
                            });
                            return resolve(false);
                        },
                        complete: function () {


                        },
                    });
                });
            }
        }
    }

</script>
@CAppEndPushScript
