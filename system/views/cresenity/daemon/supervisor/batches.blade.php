@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-batches" x-data="supervisorBatches()" x-on:beforeunload="destroyed()">
    <div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0">Batches</h2>
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

            <div x-show="ready && batches.length == 0">
                <div
                    class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                    <span>There aren't any batches.</span>
                </div>
            </div>
            <div x-show="ready && batches.length > 0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Status</th>
                            <th class="text-right">Size</th>
                            <th class="text-right">Completion</th>
                            <th class="text-right">Created</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr x-show="hasNewEntries" key="newEntries" class="dontanimate">
                            <td colspan="100" class="text-center card-bg-secondary py-2">
                                <small><a href="#" x-on:click.prevent="loadNewEntries"
                                    x-show="!loadingNewEntries">Load New Entries</a></small>

                                <small x-show="loadingNewEntries">Loading...</small>
                            </td>
                        </tr>
                        <template x-for="batch in batches" x-key="batch.id">
                            <tr>
                                <td>
                                    <a x-bind:title="batch.id" x-text="batch . name || batch . id">
                                    </a>
                                </td>
                                <td>
                                    <small class="badge badge-danger badge-sm"
                                        x-show="!batch.cancelledAt && batch.failedJobs > 0 && batch.totalJobs - batch.pendingJobs < batch.totalJobs">
                                        Failures
                                    </small>
                                    <small class="badge badge-success badge-sm"
                                        x-show="!batch.cancelledAt && batch.totalJobs - batch.pendingJobs == batch.totalJobs">
                                        Finished
                                    </small>
                                    <small class="badge badge-secondary badge-sm"
                                        x-show="!batch.cancelledAt && batch.pendingJobs > 0 && !batch.failedJobs">
                                        Pending
                                    </small>
                                    <small class="badge badge-warning badge-sm" v-if="batch.cancelledAt">
                                        Cancelled
                                    </small>
                                </td>
                                <td class="text-right text-muted" x-text="batch.totalJobs"></td>
                                <td class="text-right text-muted" x-text="batch.progress + '%'"></td>

                                <td class="text-right text-muted table-fit" x-text="formatDateIso(batch.createdAt).format('YYYY-MM-DD HH:mm:ss')">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div x-show="ready && batches.length">
                <div class="p-3 d-flex justify-content-between border-top">
                    <button @click="previous" class="btn btn-secondary btn-sm" :disabled="page == 1">Previous</button>
                    <button @click="next" class="btn btn-secondary btn-sm"
                        :disabled="batches.length < 50">Next</button>
                </div>
            </div>
        </div>

    </div>

</div>

@CAppPushScript
<script>
    window.supervisorBatches = function() {
        return {
            ready: false,
            loadingNewEntries: false,
            hasNewEntries: false,
            page: 1,
            previousFirstId: null,
            batches: [],
            ajaxBatchesUrl: '{{ $ajaxBatchesUrl }}',
            init() {
                document.title = "Supervisor - Batches";
                this.loadBatches();

                this.refreshBatchesPeriodically();


            },
            loadBatches(beforeId = '', refreshing = false) {
                if (!refreshing) {
                    this.ready = false;
                }


                this.httpGet(this.ajaxBatchesUrl + '?' + 'before_id=' + beforeId).then((data) => {
                    if (refreshing && !data.batches.length) {
                        return;
                    }
                    if (refreshing && this.batches.length && data.batches[0]?.id !== this.batches[0]?.id) {
                        this.hasNewEntries = true;
                    } else {
                        this.batches = data.batches;
                    }

                    this.ready = true;
                });
            },
            loadNewEntries() {
                this.batches = [];

                this.loadBatches(0, false);

                this.hasNewEntries = false;
            },
            /**
             * Refresh the batches every period of time.
             */
            refreshBatchesPeriodically() {
                this.interval = setInterval(() => {
                    if (this.page != 1) return;

                    this.loadBatches('', true);
                }, 3000);
            },


            previous() {
                this.loadBatches(
                    this.page == 2 ? '' : this.previousFirstId
                );


                this.page -= 1;

                this.hasNewEntries = false;
            },
            next() {
                this.previousFirstId = this.batches[0]?.id + '0';

                this.loadBatches(
                    this.batches.slice(-1)[0]?.id
                );

                this.page += 1;

                this.hasNewEntries = false;
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
            unserialized(job) {
                try {
                    return phpunserialize(this.job.payload.data.command);
                } catch (err) {
                    //
                }
            },

            delayed(job) {
                const unserialized = this.unserialized(job);
                if (unserialized && unserialized.delay && unserialized.delay.date) {
                    return moment.tz(unserialized.delay.date, unserialized.delay.timezone)
                        .fromNow(true);
                } else if (unserialized && unserialized.delay) {
                    return this.formatDate(job.payload.pushedAt).add(unserialized.delay, 'seconds')
                        .fromNow(true);
                }

                return null;
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
            /**
             * Format the given date with respect to timezone.
             */
            formatDateIso(date) {
                return moment(date).add(new Date().getTimezoneOffset() / 60);
            },
            destroyed() {
                clearInterval(this.interval);
            },
        }
    }
</script>

@CAppEndPushScript
