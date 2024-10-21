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
<div x-data="redisMetric()">
    <canvas id="{{ $id }}" style="width:100%; height:400px;"></canvas>
</div>

@CAppPushScript
<script>

    window.redisMetric = function() {

        return {
            collection : null,
            interval: null,
            maxData:20,
            datasets: @json($datasets),
            chart:null,
            pollingUrl: '{{ $pollingUrl }}',
            init() {
                if(window.cresenity) {
                    this.initChart();
                    this.fillData();
                    this.refreshPeriodically();
                    cresenity.on('reload:success',() => this.destroy());
                } else {
                    window.addEventListener('cresenity:loaded',()=>{
                        this.initChart();
                        this.fillData();
                        this.refreshPeriodically();
                        cresenity.on('reload:success',() => this.destroy());
                    });
                }

            },
            initChart() {
                this.chart = new Chart(document.getElementById('{{ $id }}').getContext("2d"), {
                    type: 'line',
                    data: [],

                    options: {
                        scales: {
                            xAxis: {
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            },
                            yAxis: {
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'MB'
                                }
                            }
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
                if(this.collection.labels.length>this.maxData) {
                    this.collection.labels = this.collection.labels.slice(-1 * this.maxData);
                }
                let index = 0;
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
                    if(this.collection.datasets[index].data.length>this.maxData) {
                        this.collection.datasets[index].data = this.collection.datasets[index].data.slice(-1 * this.maxData);
                    }
                    index++;
                }

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
