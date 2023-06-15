@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-failed" x-data="supervisorFailed()" x-destroy="destroyed()">
    <div>
        <div class="card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0">Failed Jobs</h2>

                <div class="form-control-with-icon">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <input type="text" class="form-control w-100" x-model="tagSearchPhrase" placeholder="Search Tags">
                </div>
            </div>

            <div x-show="!ready">
                <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                        <path d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
                    </svg>

                    <span>Loading...</span>
                </div>
            </div>

            <div x-show="ready && jobs.length == 0">
                <div class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                    <span>There aren't any failed jobs.</span>
                </div>
            </div>
            <div x-show="ready && jobs.length > 0">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Job</th>
                        <th class="text-right">Runtime</th>
                        <th>Failed</th>
                        <th class="text-right">Retry</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr x-show="hasNewEntries" key="newEntries" class="dontanimate">
                        <td colspan="100" class="text-center card-bg-secondary py-2">
                            <small><a href="#" x-on:click.prevent="loadNewEntries" x-show="!loadingNewEntries">Load New Entries</a></small>

                            <small x-show="loadingNewEntries">Loading...</small>
                        </td>
                    </tr>
                    <template x-for="job in jobs" x-key="job.id">
                        <tr>
                            <td>
                                <a x-bind:title="job.name" x-on:click="handleModalFailed(job.id)" x-text="jobBaseName(job.name)"></a>
                                    <template x-if="wasRetried(job)">
                                        <small class="ml-1 badge badge-secondary badge-sm"
                                            x-tooltip="retriedJobTooltip(job)"
                                        >
                                            Retried
                                        </small>
                                    </template>
                                <br>

                                <small class="text-muted">
                                    Queue: <span x-text="job.queue"></span>
                                    | Attempts: <span x-text="job.payload.attempts"></span>
                                    <template x-if="isRetry(job)">
                                        <span>
                                        | Retry of
                                        <a x-bind:title="job.name" x-text="job.payload.retry_of.split('-')[0]">
                                        </a>
                                        </span>
                                    </template>
                                    <span x-show="job.payload.tags && job.payload.tags.length" class="text-break">
                                    | Tags: <span x-text="job.payload.tags && job.payload.tags.length ? job.payload.tags.join(', ') : ''"></span>
                                    </span>
                                </small>
                            </td>

                            <td class="table-fit text-muted text-right">
                                <span x-text="job.failed_at ? String((job.failed_at - job.reserved_at).toFixed(2))+'s' : '-'"></span>
                            </td>

                            <td class="table-fit text-muted" x-text="readableTimestamp(job.failed_at)">
                            </td>

                            <td class="text-right table-fit">
                                <a href="#" title="Retry Job" @click.prevent="retry(job.id)" x-show="!hasCompleted(job)">
                                    <svg class="fill-primary" viewBox="0 0 20 20" style="width: 1.25rem; height: 1.25rem;" x-bind:class="isRetrying(job.id) ? 'spin' : ''">
                                        <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <div x-show="ready && jobs.length">
                <div class="p-3 d-flex justify-content-between border-top">
                    <button @click="previous" class="btn btn-secondary btn-sm" :disabled="page==1">Previous</button>
                    <button @click="next" class="btn btn-secondary btn-sm" :disabled="page>=totalPages">Next</button>
                </div>
            </div>
        </div>

    </div>

</div>

@CAppPushScript
<script>
    window.supervisorFailed = function() {
        return {
            tagSearchPhrase: '',
            searchTimeout: null,
            ready: false,
            loadingNewEntries: false,
            hasNewEntries: false,
            page: 1,
            perPage: 50,
            totalPages: 1,
            jobs: [],
            retryingJobs: [],
            ajaxFailedUrl: '{{ $ajaxFailedUrl }}',
            ajaxFailedRetryUrl: '{{ $ajaxFailedRetryUrl }}',
            modalFailedUrl: '{{ $modalFailedUrl }}',
            init() {
                document.title = "Supervisor - Failed Jobs";
                this.loadJobs();

                this.refreshJobsPeriodically();
                this.$watch('tagSearchPhrase', (newValue) => {
                    clearTimeout(this.searchTimeout);
                    clearInterval(this.interval);

                    this.searchTimeout = setTimeout(() => {
                        this.loadJobs();
                        this.refreshJobsPeriodically();
                    }, 500);
                });

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
            loadJobs(starting = -1, refreshing = false) {
                if (!refreshing) {
                    this.ready = false;
                }

                const tagQuery = this.tagSearchPhrase ? 'tag=' + this.tagSearchPhrase + '&' : '';
                this.httpGet(this.ajaxFailedUrl + '?' + tagQuery + 'starting_at=' + starting).then((data) => {
                    if (refreshing && !data.jobs.length) {
                        return;
                    }
                    if (refreshing && this.jobs.length && data.jobs[0]?.id !== this.jobs[0]?.id) {
                        this.hasNewEntries = true;
                    } else {
                        this.jobs = data.jobs;
                        this.totalPages = Math.ceil(data.total / this.perPage);
                    }

                    this.ready = true;
                });
            },
            loadNewEntries() {
                this.jobs = [];

                this.loadJobs(-1, false);

                this.hasNewEntries = false;
            },

            /**
             * Retry the given failed job.
             */
            retry(id) {
                if (this.isRetrying(id)) {
                    return;
                }

                this.retryingJobs.push(id);

                this.httpGet(this.ajaxFailedRetryUrl + '?jobId=' + id)
                    .then((data) => {
                        setTimeout(() => {
                            this.retryingJobs = this.retryingJobs.filter(job => job != id);
                        }, 5000);
                    }).catch(error => {
                        this.retryingJobs = this.retryingJobs.filter(job => job != id);
                    });
            },

            /**
             * Determine if the given job is currently retrying.
             */
            isRetrying(id) {
                return this.retryingJobs.includes(id);
            },

            /**
             * Determine if the given job has completed.
             */
            hasCompleted(job) {
                return job.retried_by.find(retry => retry.status === 'completed');
            },

            /**
             * Determine if the given job was retried.
             */
            wasRetried(job) {
                return job.retried_by && job.retried_by.length;
            },

            /**
             * Determine if the given job is a retry.
             */
            isRetry(job) {
                return job.payload.retry_of;
            },
            /**
             * Construct the tooltip label for a retried job.
             */
            retriedJobTooltip(job) {
                let lastRetry = job.retried_by[job.retried_by.length - 1];

                return `Total retries: ${job.retried_by.length}, Last retry status: ${this.upperFirst(lastRetry.status)}`;
            },

            /**
             * Uppercase the first character of the string.
             */
            upperFirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            },
            refreshJobsPeriodically() {
                this.interval = setInterval(() => {
                    this.loadJobs((this.page - 1) * this.perPage, true);
                }, 3000);
            },

            previous() {
                this.loadJobs(
                    (this.page - 2) * this.perPage
                );

                this.page -= 1;

                this.hasNewEntries = false;
            },
            next() {
                this.loadJobs(
                    this.page * this.perPage
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
            destroyed() {
                clearInterval(this.interval);
            },
        }
    }
</script>

@CAppEndPushScript
