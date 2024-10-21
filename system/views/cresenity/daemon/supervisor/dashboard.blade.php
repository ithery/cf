@include('cresenity.daemon.supervisor-style')

<div id="cf-supervisor-dashboard" x-data="supervisorDashboard()" x-show="ready" x-destroy="destroyed()">
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Overview</h2>
        </div>

        <div class="card-bg-secondary">
            <div class="d-flex">
                <div class="w-25">
                    <div class="p-4">
                        <small class="text-muted font-weight-bold">Jobs Per Minute</small>

                        <p class="h4 mt-2 mb-0" x-text="stats.jobsPerMinute ? stats.jobsPerMinute.toLocaleString() : 0">
                        </p>
                    </div>
                </div>

                <div class="w-25">
                    <div class="p-4">
                        <small class="text-muted font-weight-bold" x-text="recentJobsPeriod()"></small>

                        <p class="h4 mt-2 mb-0" x-text="stats.recentJobs ? stats.recentJobs.toLocaleString() : 0">
                        </p>
                    </div>
                </div>

                <div class="w-25">
                    <div class="p-4">
                        <small class="text-muted font-weight-bold" x-text="failedJobsPeriod()"></small>

                        <p class="h4 mt-2 mb-0" x-text="stats.failedJobs ? stats.failedJobs.toLocaleString() : 0">
                        </p>
                    </div>
                </div>

                <div class="w-25">
                    <div class="p-4">
                        <small class="text-muted font-weight-bold">Status</small>

                        <div class="d-flex align-items-center mt-2">
                            <svg x-show="stats.status == 'running'" xmlns="http://www.w3.org/2000/svg" class="text-success" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            <svg x-show="stats.status == 'paused'" xmlns="http://www.w3.org/2000/svg" class="text-warning" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            <svg x-show="stats.status == 'inactive'" xmlns="http://www.w3.org/2000/svg" class="text-danger" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>

                            <p class="h4 mb-0 ml-2" x-text="{running: 'Active', paused: 'Paused', inactive:'Inactive'}[stats.status]"></p>
                            <small x-show="stats.status == 'running' && stats.pausedMasters > 0" class="mb-0 ml-2" x-text="stats.pausedMasters + 'paused'"></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex">
                <div class="w-25">
                    <div class="p-4 mb-0">
                        <small class="text-muted font-weight-bold">Total Processes</small>

                        <p class="h4 mt-2" x-text="stats.processes ? stats.processes.toLocaleString() : 0">
                        </p>
                    </div>
                </div>

                <div class="w-25">
                    <div class="p-4 mb-0">
                        <small class="text-muted font-weight-bold">Max Wait Time</small>

                        <p class="mt-2 mb-0" x-text="stats.max_wait_time ? humanTime(stats.max_wait_time) : '-'">
                        </p>

                        <small class="mt-1" x-show="stats.max_wait_queue" x-text="'(' + stats.max_wait_queue + ')'">()</small>
                    </div>
                </div>

                <div class="w-25">
                    <div class="p-4 mb-0">
                        <small class="text-muted font-weight-bold">Max Runtime</small>

                        <p class="h4 mt-2" x-text="stats.queueWithMaxRuntime ? stats.queueWithMaxRuntime : '-'">
                        </p>
                    </div>
                </div>

                <div class="w-25">
                    <div class="p-4 mb-0">
                        <small class="text-muted font-weight-bold">Max Throughput</small>

                        <p class="h4 mt-2" x-text="stats.queueWithMaxThroughput ? stats.queueWithMaxThroughput : '-'">
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card overflow-hidden mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Current Workload</h2>
        </div>

        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>Queue</th>
                <th class="text-right" style="width: 120px;">Jobs</th>
                <th class="text-right" style="width: 120px;">Processes</th>
                <th class="text-right" style="width: 180px;">Wait</th>
            </tr>
            </thead>

            <tbody>
                <template x-for="queue in workload">
                    <tr>
                        <td x-bind:class="queue.split_queues ? 'font-weight-bold': ''">
                            <span x-text="queue.name.replace(/,/g, ', ')"></span>
                        </td>
                        <td
                            class="text-right text-muted"
                            x-bind:class="queue.split_queues ? 'font-weight-bold': ''"
                            x-text="queue.length ? queue.length.toLocaleString() : 0"
                        >
                        </td>
                        <td
                            class="text-right text-muted"
                            x-bind:class="queue.split_queues ? 'font-weight-bold': ''"
                            x-text="queue.processes ? queue.processes.toLocaleString() : 0"
                        >
                        </td>
                        <td
                            class="text-right text-muted"
                            x-bind:class="queue.split_queues ? 'font-weight-bold': ''"
                            x-text="humanTime(queue.wait)"
                        >
                        </td>
                    </tr>
                    <template x-for="split_queue in queue.split_queues">
                        <tr>
                            <td>
                                <svg class="icon info-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>

                                <span x-text="split_queue.name.replace(/,/g, ', ')"></span>
                            </td>
                            <td
                                class="text-right text-muted"
                                x-text="split_queue.length ? split_queue.length.toLocaleString() : 0"
                            >
                            </td>
                            <td class="text-right text-muted">-</td>
                            <td class="text-right text-muted"
                                x-text="humanTime(split_queue.wait)"
                            ></td>

                        </tr>
                    </template>


                </template>
            </tbody>
        </table>
    </div>

    <template x-for="worker in workers" x-key="worker.name">
        <div class="card overflow-hidden mt-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 m-0" x-text="worker.name"></h2>

                <svg x-show="worker.status == 'running'" xmlns="http://www.w3.org/2000/svg" class="text-success" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <svg x-show="worker.status == 'paused'" xmlns="http://www.w3.org/2000/svg" class="text-warning" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Supervisor</th>
                    <th>Queues</th>
                    <th class="text-right" style="width: 120px;">Processes</th>
                    <th class="text-right" style="width: 180px;">Balancing</th>
                </tr>
                </thead>

                <tbody>
                    <template x-for="supervisor in worker.supervisors">
                        <tr>
                            <td>
                                <svg x-show="supervisor.status == 'paused'" class="fill-warning mr-1" viewBox="0 0 20 20" style="width: 1rem; height: 1rem;">
                                    <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM7 6h2v8H7V6zm4 0h2v8h-2V6z"/>
                                </svg>
                                <span x-text="superVisorDisplayName(supervisor.name, worker.name)"></span>
                            </td>
                            <td class="text-muted" x-text="supervisor.options.queue.replace(/,/g, ', ')"></td>
                            <td class="text-right text-muted" x-text="countProcesses(supervisor.processes)"></td>
                            <td
                                class="text-right text-muted"
                                x-show="supervisor.options.balance"
                                x-text="supervisor.options.balance.charAt(0).toUpperCase() + supervisor.options.balance.slice(1)"
                            >
                            </td>
                            <td class="text-right text-muted" x-show="!supervisor.options.balance" >
                                Disabled
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>
    </template>



</div>

@CAppPushScript
<script>
window.supervisorDashboard = function() {
    return {
        stats: {},
        workers: [],
        workload: [],
        ready: false,
        ajaxStatUrl: '{{ $ajaxStatUrl }}',
        ajaxWorkloadUrl: '{{ $ajaxWorkloadUrl }}',
        ajaxWorkersUrl: '{{ $ajaxWorkersUrl }}',
        init() {
            document.title = "Supervisor - Dashboard";
            this.refreshStatsPeriodically();
        },
        recentJobsPeriod() {
            return !this.ready
                ? 'Jobs Past Hour'
                : `Jobs Past ` + this.determinePeriod(this.stats.periods.recentJobs);
        },
        failedJobsPeriod() {
            return !this.ready
                ? 'Failed Jobs Past 7 Days'
                : `Failed Jobs Past ` + this.determinePeriod(this.stats.periods.failedJobs);
        },
        refreshStatsPeriodically() {
            Promise.all([
                this.loadStats(),
                this.loadWorkers(),
                this.loadWorkload(),
            ]).then(() => {
                this.ready = true;

                this.timeout = setTimeout(() => {
                    this.refreshStatsPeriodically();
                }, 5000);
            });
        },
        async loadStats() {
            return this.httpGet(this.ajaxStatUrl).then((data) => {
                this.stats = data;
            });
        },
        async loadWorkload() {
            return this.httpGet(this.ajaxWorkloadUrl).then((data) => {
                this.workload = data;
            });
        },
        async loadWorkers() {
            return this.httpGet(this.ajaxWorkersUrl).then((data) => {
                this.workers = data;
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
                    error: (xhr, ajaxOptions, thrownError) => {
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
                    complete: () => {


                    },
                });
            });
        },
        humanTime(time) {
            const seconds = Math.floor(time);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            let durationString = "";

            if (days > 0) {
                durationString += `${days} day${days > 1 ? "s" : ""}, `;
            }
            if (hours > 0) {
                durationString += `${hours % 24} hour${hours % 24 > 1 ? "s" : ""}, `;
            }
            if (minutes > 0) {
                durationString += `${minutes % 60} minute${minutes % 60 > 1 ? "s" : ""}, `;
            }
            if (seconds > 0) {
                durationString += `${seconds % 60} second${seconds % 60 > 1 ? "s" : ""}`;
            }

            durationString = durationString.replace(/^(.)/, function ($1) {
                return $1.toUpperCase();
            });

            return durationString;
        },
        superVisorDisplayName(supervisor, worker) {
            if(!supervisor) {
                return '';
            }
            return supervisor.replace(worker + ':', '');
        },

        countProcesses(processes) {
            return Object.values(processes).reduce((total, value) => total + value, 0).toLocaleString();
        },
        determinePeriod(minutes) {
            const currentTime = new Date();
            const pastTime = new Date(currentTime.getTime() - minutes * 60 * 1000);
            const timeDiffInMs = currentTime - pastTime;

            const seconds = Math.floor(timeDiffInMs / 1000);
            const minutesDiff = Math.floor(seconds / 60);
            const hours = Math.floor(minutesDiff / 60);
            const days = Math.floor(hours / 24);

            let periodString = "";

            if (days > 0) {
                periodString += `${days} day${days > 1 ? "s" : ""}`;
            } else if (hours > 0) {
                periodString += `${hours} hour${hours > 1 ? "s" : ""}`;
            } else if (minutesDiff > 0) {
                periodString += `${minutesDiff} minute${minutesDiff > 1 ? "s" : ""}`;
            } else {
                periodString += `${seconds} second${seconds > 1 ? "s" : ""}`;
            }

            periodString = periodString.replace(/^(.)/, function ($1) {
                return $1.toUpperCase();
            });

            return periodString;
        },
        destroyed() {
            console.log('destroyed');
            clearTimeout(this.timeout);
        },

    }
}
</script>

@CAppEndPushScript
