@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-modal-failed" x-data="supervisorModalFailed()" x-destroy="destroyed()">
    <div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0" x-show="!ready">Job Preview</h2>
                <h2 class="h6 m-0" x-show="ready" x-text="job.name"></h2>

                <button class="btn btn-primary" x-on:click.prevent="retry(job.id)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon" fill="currentColor"
                        :class="{ spin: retrying }">
                        <path fill-rule="evenodd"
                            d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z"
                            clip-rule="evenodd" />
                    </svg>

                    Retry
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
                        <div class="col-md-2 text-muted">Attempts</div>
                        <div class="col" x-text="job.payload.attempts"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Retries</div>
                        <div class="col" x-text="job.retried_by.length"></div>
                    </div>
                    <template x-if="job.payload.retry_of">
                        <div class="row mb-2">
                            <div class="col-md-2 text-muted">Retry of ID</div>
                            <div class="col">
                                <a href="#" x-on:click.prevent="handleModalFailed(job.payload.retry_of)"
                                    x-text="job.payload.retry_of">
                                </a>
                            </div>
                        </div>
                    </template>
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Tags</div>
                        <div class="col"
                            x-text="job.payload.tags && job.payload.tags.length ? job.payload.tags.join(', ') : ''">
                        </div>
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
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Pushed</div>
                        <div class="col" x-text="readableTimestamp(job.payload.pushedAt)"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2 text-muted">Failed</div>
                        <div class="col" x-text="readableTimestamp(job.failed_at)"></div>
                    </div>
                    <template x-if="ready">
                        <div>
                            <div class="card overflow-hidden mt-4" v-if="ready">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h2 class="h6 m-0">Exception</h2>
                                </div>
                                <div>
                                    @include('cresenity.daemon.supervisor.component.stack-trace', ['trace'=>explode(PHP_EOL,carr::get($job,'exception'))])
                                    {{-- <x-stack-trace :trace="explode(PHP_EOL,carr::get($job,'exception'))"></x-stack-trace> --}}
                                </div>
                            </div>
                            <div class="card overflow-hidden mt-4">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h2 class="h6 m-0">Exception Context</h2>
                                </div>

                                <div class="card-body code-bg text-white">
                                    <pre x-text="JSON.stringify(prettyPrintJob(job.context), null, 2)"></pre>
                                </div>
                            </div>
                            <div class="card overflow-hidden mt-4">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h2 class="h6 m-0">Data</h2>
                                </div>

                                <div class="card-body code-bg text-white">
                                    <pre x-text="JSON.stringify(prettyPrintJob(job.payload.data), null, 2)"></pre>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="ready && job.retried_by && job.retried_by.length">
                        <div class="card overflow-hidden mt-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h2 class="h6 m-0">Recent Retries</h2>
                            </div>

                            <table class="table table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>ID</th>
                                    <th class="text-right">Retry Time</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template x-for="retry in job.retried_by">
                                    <tr>
                                        <td>
                                            <svg x-show="retry.status == 'completed'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="fill-success" style="width: 1.5rem; height: 1.5rem;">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                            </svg>

                                            <svg x-show="retry.status == 'reserved' || retry.status == 'pending'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="fill-warning" style="width: 1.5rem; height: 1.5rem;">
                                                <path fill-rule="evenodd" d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm5-2.25A.75.75 0 017.75 7h.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-.5a.75.75 0 01-.75-.75v-4.5zm4 0a.75.75 0 01.75-.75h.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-.5a.75.75 0 01-.75-.75v-4.5z" clip-rule="evenodd" />
                                            </svg>

                                            <svg x-show="retry.status == 'failed'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="fill-danger" style="width: 1.5rem; height: 1.5rem;">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>

                                            <span class="ml-2" x-text="retry.status.charAt(0).toUpperCase() + retry.status.slice(1)"></span>
                                        </td>

                                        <td class="table-fit">
                                            <a x-show="retry.status == 'failed'"
                                                x-on:click.prevent="handleModalFailed(retry.id)"
                                                x-text="retry.id"
                                            >
                                            </a>
                                            <span x-show="retry.status != 'failed'" x-text="retry.id"></span>
                                        </td>

                                        <td class="text-right table-fit text-muted" x-text="readableTimestamp(retry.retried_at)">
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>

@CAppPushScript
<script>
    window.supervisorModalFailed = function() {
        return {
            ready: false,
            retrying: false,
            job: @json($job),
            ajaxFailedDetailUrl: '{{ $ajaxFailedDetailUrl }}',
            ajaxFailedRetryUrl: '{{ $ajaxFailedRetryUrl }}',
            modalFailedUrl: '{{ $modalFailedUrl }}',
            init() {
                this.ready = true;
                this.interval = setInterval(() => {
                    this.reloadRetries();
                }, 3000);
            },
            handleModalFailed(jobId) {
                const modalFailedUrl = this.modalFailedUrl + '?jobId=' + jobId;

                cresenity.modal({
                    reload : {
                        url:modalFailedUrl
                    },
                    isSidebar:true,
                    title:'Detail Jobs ' +jobId
                });
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
            /**
             * Reload the job retries.
             */
            reloadRetries() {
                this.httpGet(this.ajaxFailedDetailUrl + '?jobId=' + this.job.id)
                    .then(data => {
                        this.job.retried_by = data.retried_by;
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

                this.httpGet(this.ajaxFailedRetryUrl + '?jobId=' + id)
                    .then(() => {
                        setTimeout(() => {
                            this.reloadRetries();

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
            },
            /**
             * Clean after the component is destroyed.
             */
            destroyed() {
                clearInterval(this.interval);
            },
        }
    }
</script>

@CAppEndPushScript
