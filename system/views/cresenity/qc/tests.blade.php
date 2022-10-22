@include('cresenity.qc.partials.tests-style-inline')
<div x-data="testingData()" class="capp-testing-container">
    <div class="card toolbar">
        <div class="card-block">
            <div class="row align-middle">
                <div class="col-md-7 align-middle">
                    <span x-bind:class="'badge badge-'+stateColor('count')" x-text="'tests: '+statistics.count"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('ok')" x-text="'success: '+statistics.success"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('failed')" x-text="'failed: '+statistics.failed"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('running')" x-text="'running: '+statistics.running"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('enabled')" x-text="'enabled: '+statistics.enabled"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('disabled')" x-text="'disabled: '+(statistics.count-statistics.enabled)"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('idle')" x-text="'idle: '+statistics.idle"></span>&nbsp;
                    <span x-bind:class="'badge badge-'+stateColor('queued')" x-text="'queued: '+statistics.queued"></span>&nbsp;
                </div>

                <div class="col-md-5 text-right align-middle">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="input-group mb-2 mb-sm-0 search-group">
                                <input x-model="filter" class="form-control" placeholder="filter">
                                <div x-show="filter" x-on:click="resetFilter" class="input-group-addon search-addon input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"><i class="fa fa-trash"></i></button>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-7" v-if="selectedProject.enabled">
                            <div class="btn btn-danger" x-on:click="runAll()">
                                run all
                            </div>
                            <div class="btn btn-warning" x-on:click="reset()">
                                reset state
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                    <input x-on:click="enableAll()" type="checkbox" x-bind:checked="allEnabled()">
                </th>
                <th width="6%">run</th>
                <th width="8%">state</th>
                <th>suite</th>
                <th width="50%">test</th>
                <th>time</th>
                <th>last</th>
                <th width="5%">log</th>
            </tr>
        </thead>

        <tbody>
            <template x-for="(test, i) in filteredTests">
                <tr x-bind:class="!test.enabled ? 'dim' : ''">
                    <td>
                        <input type="checkbox" class="testCheckbox" @click="toggleTest(test)"
                            x-bind:checked="test.enabled" />
                    </td>

                    <td>
                        <div x-on:click="runTest(test)" x-show="test.state !== 'running' && test.state !== 'queued'"
                            x-bind:class="'btn btn-sm btn-' + (test.state == 'failed' ? 'danger' : 'secondary')">
                            run
                        </div>
                    </td>

                    <td x-bind:class="'state state-' + test.state">
                        <span x-text="test.state"></span> <i x-show="test.state == 'running'"
                            class="fa fa-spinner fa-pulse  fa-spin fa-fw"></i>
                    </td>

                    <td x-text="test.suiteName">

                    </td>

                    <td>
                        <div x-on:click="editFile(test.edit_file_url)" class="table-link">
                            <span class="table-test-path mr-1" x-text="test.path"></span><span class="table-test-name"
                                x-text="test.name"></span>
                        </div>
                    </td>

                    <td x-text="test.time"></td>

                    <td x-text="test.updatedAt"></td>

                    <td>
                        <div x-on:click="showLog(test)" x-show="test.log"
                            x-bind:class="'btn btn-sm btn-' + (test.state == 'failed' ? 'primary' : 'secondary')">
                            show
                        </div>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    @include('cresenity.qc.partials.tests-log-modal')
</div>
@CAppPushScript
<script>
    function testingData() {
        return {
            tests: @json($tests),
            filter:'',
            selectedPanel:null,
            selectedTest:null,

            init() {
                this.initPolling();
            },
            showLog(test) {
                this.selectedTest = test;
                this.selectedPanel = 'log';
                jQuery('#capp-logModal').modal('show');
            },
            setPanel(panel) {
                this.selectedPanel = panel;
            },
            resetFilter() {
                this.filter = '';
            },
            get filteredTests() {

                const filtered = this.tests.filter((test)=>{
                    return test.state.toLowerCase().includes(this.filter.toLowerCase())
                        || test.name.toLowerCase().includes(this.filter.toLowerCase())
                        || test.suiteName.toLowerCase().includes(this.filter.toLowerCase())
                        || test.path.toLowerCase().includes(this.filter.toLowerCase());
                });
                return filtered;
            },
            get statistics() {
                if(!this.tests) {
                    return {};
                }
                return {
                    count : this.tests.length,
                    enabled : this.tests.filter((test) => test.enabled).length,
                    success : this.tests.filter((test) => test.state=='success').length,
                    failed : this.tests.filter((test) => test.state=='failed').length,
                    queued : this.tests.filter((test) => test.state=='queued').length,
                    running : this.tests.filter((test) => test.state=='running').length,
                    idle : this.tests.filter((test) => test.state=='idle').length,

                }
            },
            getPillColor(button) {
                if (button == this.selectedPanel) {
                    return 'btn-primary';
                }
                return 'btn-outline-primary';
            },

            stateColor(state) {
                if (state == 'running' || state == 'queued')
                {
                    return 'info';
                }
                else if (state == 'ok')
                {
                    return 'success';
                }
                else if (state == 'failed')
                {
                    return 'danger';
                }
                else if (state == 'disabled')
                {
                    return 'warning';
                }
                else if (state == 'enabled')
                {
                    return 'primary';
                }
                return 'dark';
            },
            allEnabled() {
                return this.statistics.count == this.statistics.enabled;
            },
            runAll() {
                jQuery('#capp-logModal').modal('hide');
                this.requestPost('{{ $runAllUrl }}');
            },
            reset() {
                jQuery('#capp-logModal').modal('hide');
                this.requestPost('{{ $resetUrl }}');
            },
            runTest(test) {
                jQuery('#capp-logModal').modal('hide');
                this.requestPost('{{ $runTestUrl }}',{
                    testId:test.testId
                });
            },
            async requestPost(url,data = {}) {
                return new Promise((resolve, reject) => {

                    $.ajax({
                        url: url,
                        cache: false,
                        type: 'post',
                        dataType: 'json',
                        contentType: 'application/json',
                        processData: false,
                        data: JSON.stringify(data),
                        success: function(responseData) {
                            //console.log('responseData', responseData);
                            if (typeof responseData.errCode === 'undefined') {
                                cresenity.toast('error','Unknown error');
                                return resolve(false);
                            }
                            if (responseData.errCode != 0) {
                                cresenity.toast('error',responseData.errMessage);
                                return resolve(false);
                            }
                            this.submitted = true;
                            return resolve(responseData.data);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            let error = thrownError;

                            if (typeof thrownError == 'object') {
                                console.log(JSON.stringify(thrownError));
                                error = thrownError.error;
                            }
                            cresenity.toast('error',error);
                            return resolve(false);
                        },
                        complete: function() {


                        },
                    });
                });
            },
            async loadData() {
                const data = await this.requestPost('{{ $pollUrl }}');
                this.tests = data;
            },
            initPolling() {
                setInterval(() => {
                    this.loadData();
                }, 1000);
            },
        }
    }
</script>
@CAppEndPushScript
