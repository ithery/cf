@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-modal-metrics" x-data="supervisorModalMetrics()" x-destroy="destroyed">
    <div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0">Throughput - {{ $slug }}</h2>
            </div>
            <div x-show="!ready">
                <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                        <path d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
                    </svg>

                    <span>Loading...</span>
                </div>
            </div>
            <div x-show="ready">
                <div class="card-body card-bg-secondary">
                    <p class="text-center m-0 p-5" x-show="ready && !rawData.length">
                        Not Enough Data
                    </p>

                    <canvas style="height:400px; width:100%" x-ref="throughPutChartCanvas"></canvas>


                </div>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0">Runtime - {{ $slug }}</h2>
            </div>
            <div x-show="!ready">
                <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                        <path d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
                    </svg>

                    <span>Loading...</span>
                </div>
            </div>
            <div x-show="ready">
                <div class="card-body card-bg-secondary">
                    <p class="text-center m-0 p-5" x-show="ready && !rawData.length">
                        Not Enough Data
                    </p>

                    <canvas style="height:400px; width:100%" x-ref="runTimeChartCanvas"></canvas>


                </div>
            </div>
        </div>

    </div>

</div>

@CAppPushScript
<script>
    window.supervisorModalMetrics = function() {
        return {
            ready: false,
            rawData: {},
            metric: {},
            throughPutChart:null,
            runTimeChart:null,
            ajaxMetricsUrl: '{{ $ajaxMetricsUrl }}',
            type: '{{ $type }}',
            slug: '{{ $slug }}',
            init() {
                this.loadMetric();
            },
            /**
             * Load the metric.
             */
            loadMetric() {
                this.ready = false;

                this.httpGet(this.ajaxMetricsUrl + '/' + this.type + '?slug=' + encodeURIComponent(this.slug))
                    .then(rawData => {
                        let data = this.prepareData(rawData);

                        this.rawData = rawData;

                        this.metric.throughPutChart = this.buildChartData(data, 'throughput', 'Times');

                        this.metric.runTimeChart = this.buildChartData(data, 'runtime', 'Seconds');

                        this.ready = true;
                        this.initThroughPutChart();
                        this.initRunTimeChart();
                    });
            },
            initThroughPutChart() {

                const ctx = this.$refs.throughPutChartCanvas.getContext('2d');

                this.throughPutChart = new Chart(ctx, {
                    type: 'line',
                    data: this.metric.throughPutChart,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            },
            initRunTimeChart() {

                const ctx = this.$refs.runTimeChartCanvas.getContext('2d');

                this.runTimeChart = new Chart(ctx, {
                    type: 'line',
                    data: this.metric.runTimeChart,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            },

            /**
             * Prepare the response data for charts.
             */
             prepareData(data) {
                return Object.values(this.groupBy(data.map(value => ({
                    ...value,
                    time: this.formatDate(value.time).format("MMM-D hh:mmA"),
                })), 'time')).map(value => value.reduce((sum, value) => ({
                    runtime: parseFloat(sum.runtime) + parseFloat(value.runtime),
                    throughput: parseInt(sum.throughput) + parseInt(value.throughput),
                    time: value.time
                })))
            },


            /**
             * Build the given chart data.
             */
            buildChartData(data, attribute, label) {
                return {
                    labels: data.map(entry => entry.time),
                    datasets: [
                        {
                            label: label,
                            data: data.map(entry => entry[attribute]),
                            lineTension: 0.3,
                            backgroundColor: 'transparent',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#7746ec',
                            borderColor: '#7746ec',
                            borderWidth: 2,
                        },
                    ],
                };
            },
            formatDate(unixTime) {
                return moment(unixTime * 1000).add(new Date().getTimezoneOffset() / 60);
            },

            /**
             * Group array entries by a given key.
             */
            groupBy(array, key) {
                return array.reduce(
                    (grouped, entry) => ({
                        ...grouped,
                        [entry[key]]: [...(grouped[entry[key]] || []), entry],
                    }),
                    {}
                );
            },
            async httpGet(url) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: url,
                        cache: false,
                        type: 'post',
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: (responseData) => {
                            if (typeof responseData.errCode === 'undefined') {
                                feWeb.showError('Unknown error');
                                return resolve(false);
                            }
                            if (responseData.errCode != 0) {
                                cresenity.toast('error', responseData.errMessage);
                                return resolve(false);
                            }
                            return resolve(responseData.data);
                        },
                        error: (xhr, ajaxOptions, thrownError) => {
                            let error = thrownError;

                            if (typeof thrownError == 'object') {
                                console.log(JSON.stringify(thrownError));
                                error = thrownError.error;
                            }
                            cresenity.toast('error', error);
                            return resolve(false);
                        },
                        complete: () => {


                        },
                    });
                });
            },
            destroyed() {
                if(this.throughPutChart) {
                    console.log('destroy chart');
                    this.throughPutChart.destroy();
                }
            }
        }
    }
</script>

@CAppEndPushScript
