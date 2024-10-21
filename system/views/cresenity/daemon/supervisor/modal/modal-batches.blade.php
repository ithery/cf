@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-modal-batches" x-data="supervisorModalBatches()" x-destroy="destroyed()">
    <div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0" x-show="!ready">Batch Preview</h2>
                <h2 class="h6 m-0" x-show="ready" x-text="batch.name || batch.id"></h2>

                <button class="btn btn-primary" x-show="failedJobs.length > 0" x-on:click.prevent="retry(batch.id)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon" fill="currentColor" :class="{spin: retrying}">
                        <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                    </svg>

                    Retry Failed Jobs
                </button>
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
                <div class="card-body card-bg-secondary" >
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">ID</div>
                        <div class="col">
                            <span x-text="batch.id"></span>

                            <small class="ml-1 badge badge-danger badge-sm" x-show="batch.failedJobs > 0 && batch.totalJobs - batch.pendingJobs < batch.totalJobs">
                                Failures
                            </small>
                            <small class="ml-1 badge badge-success badge-sm" x-show="batch.totalJobs - batch.pendingJobs == batch.totalJobs">
                                Finished
                            </small>
                            <small class="ml-1 badge badge-secondary badge-sm" x-show="batch.pendingJobs > 0 && !batch.failedJobs">
                                Pending
                            </small>
                        </div>
                    </div>

                    <div class="row mb-2" x-show="batch.name">
                        <div class="col-md-2 text-muted">Name</div>
                        <div class="col" x-text="batch.name"></div>
                    </div>
                    <template x-if="batch.options">
                        <div class="row mb-2" x-show="batch.options.queue">
                            <div class="col-md-2 text-muted">Queue</div>
                            <div class="col" x-text="batch.options.queue"></div>
                        </div>
                    </template>
                    <template x-if="batch.options">
                        <div class="row mb-2" x-show="batch.options.connection">
                            <div class="col-md-2 text-muted">Connection</div>
                            <div class="col" x-text="batch.options.connection"></div>
                        </div>
                    </template>
                    <template x-if="batch.createdAt">
                        <div class="row mb-2">
                            <div class="col-md-2 text-muted">Created</div>
                            <div class="col" x-text="formatDateIso(batch.createdAt).format('YYYY-MM-DD HH:mm:ss')"></div>
                        </div>
                    </template>
                    <template x-if="batch.finishedAt">
                        <div class="row mb-2">
                            <div class="col-md-2 text-muted">Finished</div>
                            <div class="col" x-text="formatDateIso(batch.finishedAt).format('YYYY-MM-DD HH:mm:ss')"></div>
                        </div>
                    </template>
                    <template x-if="batch.cancelledAt">
                        <div class="row mb-2">
                            <div class="col-md-2 text-muted">Cancelled</div>
                            <div class="col" x-text="formatDateIso(batch.cancelledAt).format('YYYY-MM-DD HH:mm:ss')"></div>
                        </div>
                    </template>
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Total Jobs</div>
                        <div class="col" x-text="batch.totalJobs"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Pending Jobs</div>
                        <div class="col" x-text="batch.pendingJobs"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Failed Jobs</div>
                        <div class="col" x-text="batch.failedJobs"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 text-muted">Completed Jobs<br><small>(Including Failed)</small></div>
                        <div class="col" x-text="(batch.totalJobs-batch.pendingJobs) + ' (' + batch.progress + '%)'"></div>
                    </div>
                </div>
            </div>
        </div>
        <template x-if="ready && failedJobs.length">
            <div class="card overflow-hidden mt-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h2 class="h6 m-0">Failed Jobs</h2>
                </div>

                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Job</th>
                        <th class="text-right">Runtime</th>
                        <th class="text-right">Failed</th>
                    </tr>
                    </thead>

                    <tbody>
                        <template x-for="failedJob in failedJobs">
                            <tr v-for="">
                                <td>
                                    <a href="#" x-on:click.prevent="handleModalFailed(failedJob.id)" x-text="jobBaseName(failedJob.name)">
                                    </a>
                                </td>

                                <td class="text-right text-muted table-fit">
                                    <span x-text="failedJob.failed_at && failedJob.reserved_at ? String((failedJob.failed_at - failedJob.reserved_at).toFixed(2))+'s' : '-'"></span>
                                </td>

                                <td class="text-right text-muted table-fit" x-text="readableTimestamp(failedJob.failed_at)">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>
    </div>

</div>

@CAppPushScript
<script>
    window.supervisorModalBatches = function() {
        return {
            ready: false,
            retrying: false,
            batch: {},
            failedJobs : [],
            ajaxBatchesRetryUrl: '{{ $ajaxBatchesRetryUrl }}',
            ajaxBatchesDetailUrl: '{{ $ajaxBatchesDetailUrl }}',
            init() {
                this.loadBatch();
                this.interval = setInterval(() => {
                    this.loadBatch(false);
                }, 3000);
            },
            loadBatch(reload = true) {
                if (reload) {
                    this.ready = false;
                }

                this.httpGet(this.ajaxBatchesDetailUrl)
                    .then(data => {
                        this.batch = data.batch;
                        this.failedJobs = data.failedJobs;

                        this.ready = true;
                    });
            },
            /**
             * Retry the given failed job.
             */
            retry(id) {
                if (this.retrying) {
                    return;
                }

                this.retrying = true;

                this.httpGet(this.ajaxBatchesRetryUrl)
                    .then(() => {
                        setTimeout(() => {
                            this.loadBatch(false);

                            this.retrying = false;
                        }, 3000);
                    });
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
            jobBaseName(name) {
                if (!name.includes('\\')) return name;

                var parts = name.split('\\');

                return parts[parts.length - 1];
            },
            formatDate(unixTime) {
                return moment(unixTime * 1000).add(new Date().getTimezoneOffset() / 60);
            },
            readableTimestamp(timestamp) {
                return this.formatDate(timestamp).format('YYYY-MM-DD HH:mm:ss');
            },
            /**
             * Clean after the component is destroyed.
             */
            destroyed() {
                clearInterval(this.interval);
            }
        }
    }
</script>

@CAppEndPushScript
