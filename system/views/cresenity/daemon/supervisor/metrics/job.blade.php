@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-metrics-job" x-data="supervisorMetricsJob()" x-destroy="destroyed()">
    <div>

        <div x-show="!ready">
            <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                    <path
                        d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z">
                    </path>
                </svg>

                <span>Loading...</span>
            </div>
        </div>

        <div x-show="ready && jobs.length == 0">
            <div
                class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <span>There aren't any jobs.</span>
            </div>
        </div>
        <div x-show="ready && jobs.length > 0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Job</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-for="job in jobs" x-key="job">
                        <tr>
                            <td>
                                <a x-bind:title="job" x-text="job" x-on:click="handleModalJobMetric(job)">
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>


    </div>

</div>

@CAppPushScript
<script>
    window.supervisorMetricsJob = function() {
        return {
            ready: false,
            jobs: @json($jobs),
            modalMetricJobUrl: '{{ $modalMetricJobUrl }}',
            init() {
                this.ready = true;
                document.title = "Supervisor - Metrics - Job";
            },

            handleModalJobMetric(job) {
                const modalMetricJobUrl = this.modalMetricJobUrl + '?slug=' + job;

                cresenity.modal({
                    reload : {
                        url:modalMetricJobUrl
                    },
                    isSidebar:true,
                    title:'Metric Jobs ' +job
                });
            },

            destroyed() {

            }
        }
    }
</script>

@CAppEndPushScript
