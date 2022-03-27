@php
if (!isset($label)) {
    $label = '[EMPTY LABEL]';
}
if (!isset($height)) {
    $height = '162px';
}
if (!isset($data)) {
    $data = [];
}
$id = uniqid();
@endphp
<div x-data="redisMetric">
    <canvas id="{{ $id }}" style="width:100%; height:100%; min-height:400px"></canvas>
</div>

@CAppPushScript
<script>

    window.redisMetric = function() {
        return {
            collection : null,
            interval: null,
            datasets: @json($datasets),
            chart:null,
            pollingUrl: '{{ $pollingUrl }}',
            init() {
                window.addEventListener('cresenity:loaded',()=>{
                    this.fillData();
                    this.refreshPeriodically();
                    this.initChart();

                })
            },
            initChart() {
                this.chart = new Chart(document.getElementById('{{ $id }}').getContext("2d"), {
                    type: 'line',
                    data: [],

                    options: {
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'MB'
                                }
                            }]
                        },
                        legend: {
                            display: true
                        },
                        responsive: false,
                        maintainAspectRatio: false
                    }
                });
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
                if(this.collection==null) {
                    this.collection = {
                        labels: [],
                        datasets: [],
                    }

                }
                this.collection.labels.push(this.time());
                let index=0;
                for(let i in this.datasets) {

                    if(this.collection.datasets.length < index+1) {
                        this.collection.datasets[index] = {
                            label: this.datasets[i].key,
                            fill: false,
                            backgroundColor: this.datasets[i].color,
                            borderColor: this.datasets[i].color,
                            data:[],
                        }
                    }
                    this.collection.datasets[index].data.push(response[this.datasets[i].key]);
                    index++;
                }
                console.log(this.collection);
                this.chart.data.labels = this.collection.labels;
                this.chart.data.datasets = this.collection.datasets;
                this.chart.update();
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
