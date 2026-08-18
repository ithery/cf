@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-modal-jobs" x-data="supervisorModalJobs()">
    <div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0" x-show="!ready">Job Preview</h2>
                <h2 class="h6 m-0" x-show="ready" x-text="job.name"></h2>

                <a data-toggle="collapse" href="#collapseDetails" role="button">
                    Collapse
                </a>
            </div>
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
            <div x-show="ready">
                <div class="card-body card-bg-secondary collapse show" id="collapseDetails">
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">ID</div>
                        <div class="col" x-text="job.id"></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Queue</div>
                        <div class="col" x-text="job.queue"></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Pushed</div>
                        <div class="col" x-text="readableTimestamp(job.payload.pushedAt)"></div>
                    </div>
                    <template x-if="prettyPrintJob(job.payload.data).batchId">
                        <div class="row mb-2">
                            <div class="col-md-2 text-muted">Batch</div>
                            <div class="col">
                                <a x-text="prettyPrintJob(job.payload.data).batchId">
                                </a>
                            </div>
                        </div>
                    </template>
                    <div class="row mb-2" v-if="delayed">
                        <div class="col-md-2 text-muted">Delayed Until</div>
                        <div class="col" x-text="delayed()"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 text-muted">Completed</div>
                        <div class="col" x-text="job.completed_at ? readableTimestamp(job.completed_at) : '-'"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card overflow-hidden mt-4" x-show="ready">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Data</h2>

            <a data-toggle="collapse" href="#collapseData" role="button">
                Collapse
            </a>
        </div>

        <div class="card-body code-bg text-white collapse show" id="collapseData">
            <pre x-text="JSON.stringify(prettyPrintJob(job.payload.data), null, 2)"></pre>
        </div>
    </div>

    <div class="card overflow-hidden mt-4" x-show="ready && job.payload.tags.length">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Tags</h2>

            <a data-toggle="collapse" href="#collapseTags" role="button">
                Collapse
            </a>
        </div>

        <div class="card-body code-bg text-white collapse show" id="collapseTags">
            <pre x-text="JSON.stringify(job.payload.tags, null, 2)"></pre>
        </div>
    </div>
</div>

@CAppPushScript
<script>
    window.supervisorModalJobs = function() {
        return {
            ready: false,
            job: @json($job),
            init() {
                this.ready = true;
            },
            formatDate(unixTime) {
                return moment(unixTime * 1000).add(new Date().getTimezoneOffset() / 60);
            },
            readableTimestamp(timestamp) {
                return this.formatDate(timestamp).format('YYYY-MM-DD HH:mm:ss');
            },
            /**
             * Pretty print serialized job.
             */
            prettyPrintJob(data) {
                try {
                    return data.command && !data.command.includes('CallQueuedClosure') ?
                        cresenity.php.unserialize(data.command) : data;
                } catch (err) {
                    return data;
                }
            },
            delayed() {
                let unserialized;

                try {
                    unserialized = cresenity.php.unserialize(this.job.payload.data.command);

                } catch (err) {
                    //
                }

                if (unserialized && unserialized.delay && unserialized.delay.date) {
                    return moment.tz(unserialized.delay.date, unserialized.delay.timezone)
                        .local()
                        .format('YYYY-MM-DD HH:mm:ss');
                } else if (unserialized && unserialized.delay) {
                    return this.formatDate(this.job.payload.pushedAt).add(unserialized.delay, 'seconds')
                        .local()
                        .format('YYYY-MM-DD HH:mm:ss');
                }

                return null;
            }
        }
    }
</script>

@CAppEndPushScript
