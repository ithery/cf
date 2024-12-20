<div class="modal fade" tabIndex="-1" id="capp-logModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" v-if="selectedTest">
            <div class="modal-header">
                <h3 class="modal-title" x-text="selectedTest.name">

                </h3>

                <h3>
                    <span x-bind:class="'pull-right badge badge-'+(selectedTest.state == 'failed' ? 'danger' : (selectedTest.state == 'ok' ? 'success' : (selectedTest.state == 'running' || selectedTest.state == 'queued' ? 'warning' : 'secondary')))">
                        <i x-show="selectedTest.state == 'running'" class="fa fa-cog fa-spin fa-fw"></i>
                        <i x-show="selectedTest.state == 'queued'" class="fa fa-clock"></i>
                        <span x-text="selectedTest.state"></span>
                    </span>
                </h3>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-8">
                        <div x-show="selectedTest.log" x-bind:class="'btn btn-pill ' + getPillColor('log')" x-on:click="setPanel('log')">
                            command output
                        </div>
                        &nbsp;
                        <div x-show="selectedTest.run.screenshots"  x-bind:class="'btn btn-pill ' + getPillColor('screenshot')" x-on:click="setPanel('screenshot')">
                            screenshots
                        </div>
                        &nbsp;
                        <div x-show="selectedTest.coverage.enabled && false"  x-bind:class="'btn btn-pill ' + getPillColor('coverage')" x-on:click="setPanel('coverage')">
                            coverage
                        </div>
                        &nbsp;
                        <div x-show="selectedTest.html" x-bind:class="'btn btn-pill ' + getPillColor('html')" x-on:click="setPanel('html')">
                            <span x-text="getHtmlPanelName()"></span>
                        </div>
                    </div>

                    <div class="col-4 text-right">
                        <button x-bind:disabled="selectedTest.state == 'running' || selectedTest.state == 'queued'" x-on:click="runTest(selectedTest)" x-bind:class="'btn btn-sm btn-'+(selectedTest.state !== 'running' && selectedTest.state !== 'queued' ? 'danger' : 'secondary')">
                            <i class="fa fa-play"></i> run it
                        </button>
                        <button class="btn btn-info" x-on:click="showCode(selectedTest)">
                            <i class="fa fa-file"></i> show code
                        </button>

                    </div>
                </div>

                <div x-bind:class="'tab-content modal-scroll' + (selectedPanel == 'log' ? ' terminal' : '')  + (selectedPanel == 'html' ? ' html' : '')">
                    <div x-show="selectedPanel == 'log'" x-html="selectedTest.log" x-bind:class="'tab-pane terminal ' + (selectedPanel == 'log' ? 'active' : '')">
                    </div>

                    <div x-show="selectedPanel == 'screenshot'" x-bind:class="'tab-pane ' + (selectedPanel == 'screenshot' ? 'active' : '')">
                        <div v-for="screenshot in JSON.parse(selectedTest.run.screenshots)" class="text-center">
                            <h3 x-text="String(screenshot).substring(screenshot.lastIndexOf('/') + 1)"></h3>
                            <img x-bind:src="makeScreenshot(screenshot)" alt="screenshot" class="screenshot"/>
                        </div>
                    </div>

                    <div v-if="selectedPanel == 'coverage" x-bind:class="'tab-pane ' + (selectedPanel == 'coverage' ? 'active' : '')">
                        <iframe
                            id="serviceFrameSend"
                            x-bind:src="'/html?index=' + selectedTest.coverage.index"
                            width="100%"
                            height="1000"
                            frameborder="0">
                        </iframe>
                    </div>

                    <div v-if="selectedPanel == 'html'"  v-html="selectedTest.html" x-bind:class="'tab-pane ' + (selectedPanel == 'html' ? 'active' : '')">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="btn btn-success" data-dismiss="modal">
                    close
                </div>
            </div>
        </div>
    </div>
</div>
