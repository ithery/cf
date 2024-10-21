@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-jobs" x-data="supervisorJobs()" x-destroy="destroyed()">
    <div>

        <div x-show="!ready">
            <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                    <path
                        d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
                </svg>

                <span>Loading...</span>
            </div>
        </div>
        <div x-show="ready && jobs.length == 0">
            <div class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <span>There aren't any jobs.</span>
            </div>
        </div>

        <div x-show="ready && jobs.length > 0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Job</th>
                        <th x-show="type=='pending'" class="text-right">Queued</th>
                        <th x-show="type=='completed' || type=='silenced'">Queued</th>
                        <th x-show="type=='completed' || type=='silenced'">Completed</th>
                        <th x-show="type=='completed' || type=='silenced'" class="text-right">Runtime</th>
                    </tr>
                </thead>

                <tbody>

                    <tr x-show="hasNewEntries" key="newEntries" class="dontanimate">
                        <td colspan="100" class="text-center card-bg-secondary py-1">
                            <small>
                                <a href="#"
                                    x-on:click.prevent="loadNewEntries"
                                    x-show="!loadingNewEntries"
                                >Load New Entries
                                </a>
                            </small>

                            <small x-show="loadingNewEntries">Loading...</small>
                        </td>
                    </tr>
                    <template x-for="job in jobs" x-key="job.id">
                        <tr>
                            <td>
                                <a href="#" x-bind:title="job.name" x-on:click="handleModalJob(job)" x-text="jobBaseName(job.name)">
                                </a>

                                <small class="ml-1 badge badge-secondary badge-sm"
                                        x-tooltip="`Delayed for ` + delayed(job)"
                                        x-show="delayed(job) && (job.status == 'reserved' || job.status == 'pending')">
                                    Delayed
                                </small>

                                <br>

                                <small class="text-muted">
                                    Queue: <span x-text="job.queue"></span>

                                    <span x-show="job.payload.tags && job.payload.tags.length" class="text-break">
                                        | Tags: <span x-text="job.payload.tags && job.payload.tags.length ? job.payload.tags.slice(0,3).join(', ') : ''"></span>
                                        <span x-show="job.payload.tags.length > 3" x-text="'(' + job.payload.tags.length - 3 + ') more'"></span>
                                    </span>
                                </small>
                            </td>

                            <td class="table-fit text-muted" x-text="readableTimestamp(job.payload.pushedAt)">
                            </td>

                            <td x-show="type=='completed' || type=='silenced'" class="table-fit text-muted"
                                x-text="readableTimestamp(job.completed_at)"
                            >
                            </td>

                            <td x-show="type=='completed' || type=='silenced'" class="table-fit text-right text-muted"
                                x-text="job.completed_at ? (job.completed_at - job.reserved_at).toFixed(2)+'s' : '-'"
                            >
                            </td>
                        </tr>
                    </template>

                    </tr>
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

@CAppPushScript
<script>
window.supervisorJobs = function() {
    return {
        ready: false,
        loadingNewEntries: false,
        hasNewEntries: false,
        page: 1,
        perPage: 50,
        totalPages: 1,
        jobs: [],
        ajaxJobsUrl: '{{ $ajaxJobsUrl }}',
        modalJobsUrl: '{{ $modalJobsUrl }}',
        type: '{{ $type }}',
        init() {
            const typeTitle = this.type == 'pending'
                ? 'Pending Jobs'
                : (
                    this.type == 'silenced'
                        ? 'Silenced Jobs'
                        : 'Completed Jobs'
                );
            document.title = "Supervisor - Jobs - " + typeTitle;
            this.loadJobs();

            this.refreshJobsPeriodically();
        },
        handleModalJob(job) {
            const modalJobsUrl = this.modalJobsUrl + '?jobId=' + job.id;

            cresenity.modal({
                reload : {
                    url:modalJobsUrl
                },
                isSidebar:true,
                title:'Detail Jobs ' + this.jobBaseName(job.name)
            });
        },
        loadJobs(starting=-1, refreshing = false) {
            if (!refreshing) {
                this.ready = false;
            }
            this.httpGet(this.ajaxJobsUrl + '?starting_at=' + starting + '&limit=' + this.perPage).then((data) => {
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


        refreshJobsPeriodically() {
            this.interval = setInterval(() => {
                if (this.page != 1) {
                    return;
                }

                this.loadJobs(-1, true);
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
            }catch(err){
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
                        if(typeof responseData.errCode === 'undefined') {
                            feWeb.showError('Unknown error');
                            return resolve(false);
                        }
                        if(responseData.errCode != 0) {
                            cresenity.toast('error',responseData.errMessage);
                            return resolve(false);
                        }
                        return resolve(responseData.data);
                    },
                    error: (xhr, ajaxOptions, thrownError) => {
                        let error = thrownError;

                        if(typeof thrownError == 'object') {
                            console.log(JSON.stringify(thrownError));
                            error = thrownError.error;
                        }
                        cresenity.toast('error',error);
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
