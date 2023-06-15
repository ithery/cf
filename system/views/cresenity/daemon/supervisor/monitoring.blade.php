@include('cresenity.daemon.supervisor-style')
<div id="cf-supervisor-monitoring" x-data="supervisorMonitoring()" x-on:beforeunload="destroyed()">
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Monitoring</h2>

            {{-- <button @click="openNewTagModal" class="btn btn-primary btn-sm">Monitor Tag</button> --}}
        </div>

        <div x-show="!ready">
            <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                    <path d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
                </svg>

                <span>Loading...</span>
            </div>
        </div>

        <div x-show="ready && tags.length == 0">
            <div class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <span>You're not monitoring any tags.</span>
            </div>
        </div>

        <div x-show="ready && tags.length > 0">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Tag</th>
                    <th class="text-right">Jobs</th>
                    <th class="text-right"></th>
                </tr>
                </thead>

                <tbody>
                    <template x-for="tag in tags">
                        <tr>
                            <td>
                                <a href="#" x-text="tag.tag">
                                </a>
                            </td>
                            <td class="text-right text-muted" x-text="tag.count"></td>
                            <td class="text-right">
                                <a href="#" x-on:click="stopMonitoring(tag.tag)" class="control-action" title="Stop Monitoring">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
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
window.supervisorMonitoring = function() {
    return {
        ready: false,
        newTag: '',
        addTagModalOpened: false,
        tags: [],
        ajaxTagsUrl: '{{ $ajaxTagsUrl }}',
        init() {
            document.title = "Supervisor - Monitoring";
            this.loadTags();
            this.refreshTagsPeriodically();
        },
        loadTags() {
            this.httpGet(this.ajaxTagsUrl).then((data) => {
                this.tags = data;
                this.ready = true;
            });
        },
        refreshTagsPeriodically() {
            this.interval = setInterval(() => {
                this.loadTags();
            }, 3000);
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
        destroyed() {
            clearInterval(this.interval);
        },
    }
}
</script>

@CAppEndPushScript
