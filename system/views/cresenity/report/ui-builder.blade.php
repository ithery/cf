<style>
    .cr-rbuilder { display: flex; flex-direction: column; gap: 10px; font-size: 13px; }
    .cr-rbuilder * { box-sizing: border-box; }
    .cr-rb-toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding: 8px; border: 1px solid #d9dee3; border-radius: 6px; background: #fff; }
    .cr-rb-toolbar .form-control, .cr-rb-toolbar .form-select { width: auto; display: inline-block; }
    .cr-rb-body { display: flex; gap: 10px; align-items: stretch; min-height: 600px; }
    .cr-rb-left { width: 220px; flex: 0 0 220px; display: flex; flex-direction: column; gap: 10px; }
    .cr-rb-right { width: 300px; flex: 0 0 300px; }
    .cr-rb-panel { border: 1px solid #d9dee3; border-radius: 6px; background: #fff; }
    .cr-rb-panel-title { padding: 6px 10px; font-weight: 600; border-bottom: 1px solid #e6e9ec; background: #f6f8fa; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .cr-rb-panel-body { padding: 8px 10px; }
    .cr-rb-palette-item { border: 1px solid #cfd6dd; border-radius: 4px; padding: 6px 8px; margin-bottom: 6px; cursor: pointer; background: #fff; user-select: none; display: flex; gap: 8px; align-items: center; }
    .cr-rb-palette-item:hover { background: #eef4fb; }
    .cr-rb-palette-item.active { background: #d8e8fb; border-color: #5b9bd5; }
    .cr-rb-palette-item .ti { color: #5b7083; }
    .cr-rb-list-item { display: flex; justify-content: space-between; align-items: center; padding: 4px 6px; border: 1px solid #e2e6ea; border-radius: 4px; margin-bottom: 4px; cursor: pointer; }
    .cr-rb-list-item:hover, .cr-rb-list-item.active { background: #eef4fb; }
    .cr-rb-band-toggle { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
    .cr-rb-canvas-wrap { flex: 1 1 auto; overflow: auto; border: 1px solid #d9dee3; border-radius: 6px; background: #e9ecef; padding: 16px; }
    .cr-rb-page { background: #fff; margin: 0 auto; box-shadow: 0 1px 4px rgba(0,0,0,.25); }
    .cr-rb-band-row { position: relative; }
    .cr-rb-band-label { font-size: 10px; color: #7a8894; background: #f2f5f7; border-top: 1px solid #dfe4e8; border-bottom: 1px solid #dfe4e8; padding: 1px 6px; cursor: pointer; user-select: none; }
    .cr-rb-band-label:hover { background: #e3ecf5; }
    .cr-rb-band-label.selected { background: #d8e8fb; color: #24547e; }
    .cr-rb-band-area { position: relative; overflow: hidden; background-image: linear-gradient(to right, rgba(0,0,0,.045) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,.045) 1px, transparent 1px); background-size: 10px 10px; }
    .cr-rb-band-area.armed { cursor: crosshair; }
    .cr-rb-el { position: absolute; border: 1px dashed transparent; cursor: move; overflow: hidden; white-space: pre; }
    .cr-rb-el:hover { border-color: #9db8d2; }
    .cr-rb-el.selected { border: 1px solid #2e77c9; }
    .cr-rb-el .cr-rb-handle { position: absolute; right: 0; bottom: 0; width: 8px; height: 8px; background: #2e77c9; cursor: nwse-resize; display: none; }
    .cr-rb-el.selected .cr-rb-handle { display: block; }
    .cr-rb-props label { font-size: 11px; color: #5b7083; margin-bottom: 0; display: block; }
    .cr-rb-props .form-control, .cr-rb-props .form-select { font-size: 12px; padding: 2px 6px; height: auto; }
    .cr-rb-props-row { display: flex; gap: 6px; margin-bottom: 6px; }
    .cr-rb-props-row > div { flex: 1; min-width: 0; }
    .cr-rb-modal-back { position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1050; display: flex; align-items: center; justify-content: center; }
    .cr-rb-modal { background: #fff; border-radius: 8px; width: min(860px, 92vw); max-height: 88vh; display: flex; flex-direction: column; }
    .cr-rb-modal-head { padding: 10px 14px; border-bottom: 1px solid #e6e9ec; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
    .cr-rb-modal-body { padding: 14px; overflow: auto; }
    .cr-rb-modal textarea { width: 100%; min-height: 380px; font-family: monospace; font-size: 12px; }
    .cr-rb-muted { color: #8a97a3; font-size: 11px; }
    .cr-rb-btn-xs { padding: 1px 7px; font-size: 11px; line-height: 1.4; }
</style>

<div class="cr-rbuilder" x-data="crReportBuilder()" x-init="init()">
    <!-- Toolbar -->
    <div class="cr-rb-toolbar">
        <strong>Report UI Builder</strong>
        <span class="cr-rb-muted">1 unit = 1 point</span>
        <span style="flex:1"></span>
        <button type="button" class="btn btn-sm btn-outline-secondary" x-on:click="loadSample()">Load Sample</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" x-on:click="openLoad()">Load JRXML</button>
        <button type="button" class="btn btn-sm btn-primary" x-on:click="openOutput()">Generate JRXML</button>
    </div>

    <div class="cr-rb-body">
        <!-- Left: palette / bands / groups / variables -->
        <div class="cr-rb-left">
            <div class="cr-rb-panel">
                <div class="cr-rb-panel-title">Palette</div>
                <div class="cr-rb-panel-body">
                    <template x-for="tool in palette" :key="tool.type">
                        <div class="cr-rb-palette-item" draggable="true"
                            :class="{active: armedTool === tool.type}"
                            x-on:click="armedTool = (armedTool === tool.type ? null : tool.type)"
                            x-on:dragstart="paletteDragStart($event, tool.type)"
                            x-on:dragend="dragTool = null">
                            <i class="ti" :class="tool.icon"></i>
                            <span x-text="tool.label"></span>
                        </div>
                    </template>
                    <div class="cr-rb-muted">Drag a tool onto a band, or click the tool then click a band.</div>
                </div>
            </div>

            <div class="cr-rb-panel">
                <div class="cr-rb-panel-title">Bands</div>
                <div class="cr-rb-panel-body">
                    <template x-for="def in bandDefs" :key="def.type">
                        <div class="cr-rb-band-toggle">
                            <input type="checkbox" :id="'cr-rb-band-' + def.type" x-model="bands[def.type].enabled">
                            <label :for="'cr-rb-band-' + def.type" style="cursor:pointer" x-text="def.label"></label>
                        </div>
                    </template>
                </div>
            </div>

            <div class="cr-rb-panel">
                <div class="cr-rb-panel-title">
                    Groups
                    <button type="button" class="btn btn-outline-primary cr-rb-btn-xs" x-on:click="addGroup()">+ Add</button>
                </div>
                <div class="cr-rb-panel-body">
                    <template x-for="g in groups" :key="g.id">
                        <div class="cr-rb-list-item" :class="{active: sel.kind === 'group' &amp;&amp; sel.group === g}" x-on:click="selectGroup(g)">
                            <span x-text="g.name"></span>
                            <button type="button" class="btn btn-outline-danger cr-rb-btn-xs" x-on:click.stop="removeGroup(g)">&times;</button>
                        </div>
                    </template>
                    <div class="cr-rb-muted" x-show="groups.length === 0">No group. Group breaks on expression change.</div>
                </div>
            </div>

            <div class="cr-rb-panel">
                <div class="cr-rb-panel-title">
                    Variables
                    <button type="button" class="btn btn-outline-primary cr-rb-btn-xs" x-on:click="addVariable()">+ Add</button>
                </div>
                <div class="cr-rb-panel-body">
                    <template x-for="v in variables" :key="v.id">
                        <div class="cr-rb-list-item" :class="{active: sel.kind === 'variable' &amp;&amp; sel.variable === v}" x-on:click="selectVariable(v)">
                            <span x-text="v.name"></span>
                            <button type="button" class="btn btn-outline-danger cr-rb-btn-xs" x-on:click.stop="removeVariable(v)">&times;</button>
                        </div>
                    </template>
                    <div class="cr-rb-muted" x-show="variables.length === 0">No variable. Use as $V{name}.</div>
                </div>
            </div>
        </div>

        <!-- Center: canvas -->
        <div class="cr-rb-canvas-wrap" x-on:click.self="selectReport()">
            <div class="cr-rb-page" :style="'width:' + report.pageWidth + 'px; padding:' + report.topMargin + 'px ' + report.rightMargin + 'px ' + report.bottomMargin + 'px ' + report.leftMargin + 'px;'">
                <template x-for="row in visibleBands()" :key="row.key">
                    <div class="cr-rb-band-row">
                        <div class="cr-rb-band-label" :class="{selected: sel.kind === 'band' &amp;&amp; sel.band === row.band}" x-on:click="selectBand(row)" x-text="row.label + '  (h=' + row.band.height + ')'"></div>
                        <div class="cr-rb-band-area" :class="{armed: armedTool || dragTool}" :style="'height:' + row.band.height + 'px; width:' + contentWidth() + 'px;'"
                            x-on:mousedown="placeElement($event, row.band)"
                            x-on:dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                            x-on:drop.prevent="paletteDrop($event, row.band)">
                            <template x-for="el in row.band.elements" :key="el.id">
                                <div class="cr-rb-el" :class="{selected: sel.kind === 'element' &amp;&amp; sel.element === el}" :style="elStyle(el)" x-on:mousedown.stop="startDrag($event, row.band, el, 'move')">
                                    <span x-text="elDisplayText(el)"></span>
                                    <div class="cr-rb-handle" x-on:mousedown.stop="startDrag($event, row.band, el, 'resize')"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right: properties -->
        <div class="cr-rb-right">
            <div class="cr-rb-panel cr-rb-props">
                <div class="cr-rb-panel-title" x-text="propsTitle()"></div>
                <div class="cr-rb-panel-body">
                    <!-- Report properties -->
                    <div x-show="sel.kind === 'report'">
                        <div class="cr-rb-props-row"><div><label>Report Name</label><input class="form-control" x-model="report.name"></div></div>
                        <div class="cr-rb-props-row">
                            <div>
                                <label>Paper</label>
                                <select class="form-select" x-on:change="applyPaper($event.target.value)">
                                    <option value="">Custom</option>
                                    <option value="A4">A4</option>
                                    <option value="A4L">A4 Landscape</option>
                                    <option value="LETTER">Letter</option>
                                    <option value="LEGAL">Legal</option>
                                </select>
                            </div>
                        </div>
                        <div class="cr-rb-props-row">
                            <div><label>Page Width</label><input type="number" class="form-control" x-model.number="report.pageWidth"></div>
                            <div><label>Page Height</label><input type="number" class="form-control" x-model.number="report.pageHeight"></div>
                        </div>
                        <div class="cr-rb-props-row">
                            <div><label>Margin Left</label><input type="number" class="form-control" x-model.number="report.leftMargin"></div>
                            <div><label>Margin Right</label><input type="number" class="form-control" x-model.number="report.rightMargin"></div>
                        </div>
                        <div class="cr-rb-props-row">
                            <div><label>Margin Top</label><input type="number" class="form-control" x-model.number="report.topMargin"></div>
                            <div><label>Margin Bottom</label><input type="number" class="form-control" x-model.number="report.bottomMargin"></div>
                        </div>
                        <div class="cr-rb-muted">columnWidth is generated automatically from page width minus margins.</div>
                    </div>

                    <!-- Band properties -->
                    <template x-if="sel.kind === 'band' &amp;&amp; sel.band">
                        <div>
                            <div class="cr-rb-props-row"><div><label>Height</label><input type="number" class="form-control" x-model.number="sel.band.height"></div></div>
                            <div class="cr-rb-muted">Elements: <span x-text="sel.band.elements.length"></span></div>
                        </div>
                    </template>

                    <!-- Group properties -->
                    <template x-if="sel.kind === 'group' &amp;&amp; sel.group">
                    <div>
                        <div class="cr-rb-props-row"><div><label>Name</label><input class="form-control" x-model="sel.group.name"></div></div>
                        <div class="cr-rb-props-row"><div><label>Group Expression</label><input class="form-control" x-model="sel.group.expression" placeholder="$F{invoice_no}"></div></div>
                        <div class="cr-rb-props-row">
                            <div><label><input type="checkbox" x-model="sel.group.reprint"> Reprint header on each page</label></div>
                        </div>
                        <div class="cr-rb-props-row">
                            <div><label><input type="checkbox" x-model="sel.group.header.enabled"> Group Header</label><input type="number" class="form-control" x-model.number="sel.group.header.height"></div>
                            <div><label><input type="checkbox" x-model="sel.group.footer.enabled"> Group Footer</label><input type="number" class="form-control" x-model.number="sel.group.footer.height"></div>
                        </div>
                    </div>
                    </template>

                    <!-- Variable properties -->
                    <template x-if="sel.kind === 'variable' &amp;&amp; sel.variable">
                    <div>
                        <div class="cr-rb-props-row"><div><label>Name</label><input class="form-control" x-model="sel.variable.name"></div></div>
                        <div class="cr-rb-props-row">
                            <div>
                                <label>Class</label>
                                <select class="form-select" x-model="sel.variable.varClass">
                                    <option value="java.lang.Double">Double</option>
                                    <option value="java.lang.Integer">Integer</option>
                                    <option value="java.lang.String">String</option>
                                </select>
                            </div>
                            <div>
                                <label>Calculation</label>
                                <select class="form-select" x-model="sel.variable.calculation">
                                    <option value="Sum">Sum</option>
                                    <option value="Nothing">Nothing</option>
                                </select>
                            </div>
                        </div>
                        <div class="cr-rb-props-row"><div><label>Variable Expression</label><input class="form-control" x-model="sel.variable.expression" placeholder="$F{subtotal}"></div></div>
                        <div class="cr-rb-props-row"><div><label>Initial Value Expression</label><input class="form-control" x-model="sel.variable.initialValue"></div></div>
                        <div class="cr-rb-props-row">
                            <div>
                                <label>Reset Type</label>
                                <select class="form-select" x-model="sel.variable.resetType">
                                    <option value="Report">Report</option>
                                    <option value="Group">Group</option>
                                    <option value="Page">Page</option>
                                    <option value="None">None</option>
                                </select>
                            </div>
                            <div>
                                <label>Reset Group</label>
                                <select class="form-select" x-model="sel.variable.resetGroup">
                                    <option value="">-</option>
                                    <template x-for="g in groups" :key="g.id"><option :value="g.name" x-text="g.name"></option></template>
                                </select>
                            </div>
                        </div>
                    </div>
                    </template>

                    <!-- Element properties -->
                    <div x-show="sel.kind === 'element'">
                        <template x-if="sel.element">
                            <div>
                                <div class="cr-rb-props-row">
                                    <div><label>Type</label><input class="form-control" :value="sel.element.type" disabled></div>
                                </div>
                                <div class="cr-rb-props-row">
                                    <div><label>X</label><input type="number" class="form-control" x-model.number="sel.element.x"></div>
                                    <div><label>Y</label><input type="number" class="form-control" x-model.number="sel.element.y"></div>
                                    <div><label>W</label><input type="number" class="form-control" x-model.number="sel.element.width"></div>
                                    <div><label>H</label><input type="number" class="form-control" x-model.number="sel.element.height"></div>
                                </div>

                                <div x-show="sel.element.type === 'staticText'">
                                    <div class="cr-rb-props-row"><div><label>Text</label><textarea class="form-control" rows="2" x-model="sel.element.text"></textarea></div></div>
                                </div>
                                <div x-show="sel.element.type === 'textField'">
                                    <div class="cr-rb-props-row"><div><label>Expression</label><textarea class="form-control" rows="2" x-model="sel.element.expression" placeholder='$F{field} , $V{var} , "text " + $F{field}'></textarea></div></div>
                                    <div class="cr-rb-props-row">
                                        <div>
                                            <label>Pattern</label>
                                            <select class="form-select" x-model="sel.element.pattern">
                                                <option value="">-</option>
                                                <option value="#,##0">#,##0</option>
                                                <option value="#,##0.00">#,##0.00</option>
                                                <option value="###0">###0</option>
                                                <option value="###0.00">###0.00</option>
                                                <option value="dd/MM/yyyy">dd/MM/yyyy</option>
                                                <option value="MM/dd/yyyy">MM/dd/yyyy</option>
                                                <option value="yyyy/MM/dd">yyyy/MM/dd</option>
                                                <option value="dd/MM/yyyy HH:mm">dd/MM/yyyy HH:mm</option>
                                            </select>
                                        </div>
                                        <div><label><input type="checkbox" x-model="sel.element.stretch"> Stretch height</label></div>
                                    </div>
                                </div>

                                <div x-show="sel.element.type === 'staticText' || sel.element.type === 'textField'">
                                    <div class="cr-rb-props-row">
                                        <div><label>Font Size</label><input type="number" class="form-control" x-model.number="sel.element.fontSize"></div>
                                        <div>
                                            <label>Style</label>
                                            <div style="display:flex; gap:8px;">
                                                <label><input type="checkbox" x-model="sel.element.bold"> B</label>
                                                <label><input type="checkbox" x-model="sel.element.italic"> I</label>
                                                <label><input type="checkbox" x-model="sel.element.underline"> U</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cr-rb-props-row">
                                        <div>
                                            <label>H Align</label>
                                            <select class="form-select" x-model="sel.element.hAlign">
                                                <option>Left</option><option>Center</option><option>Right</option><option>Justified</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label>V Align</label>
                                            <select class="form-select" x-model="sel.element.vAlign">
                                                <option>Top</option><option>Middle</option><option>Bottom</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="cr-rb-props-row">
                                        <div>
                                            <label>Borders</label>
                                            <div style="display:flex; gap:8px;">
                                                <label><input type="checkbox" x-model="sel.element.borders.top"> T</label>
                                                <label><input type="checkbox" x-model="sel.element.borders.bottom"> B</label>
                                                <label><input type="checkbox" x-model="sel.element.borders.left"> L</label>
                                                <label><input type="checkbox" x-model="sel.element.borders.right"> R</label>
                                            </div>
                                        </div>
                                        <div><label>Border Width</label><input type="number" step="0.5" class="form-control" x-model.number="sel.element.borderWidth"></div>
                                    </div>
                                </div>

                                <div x-show="sel.element.type === 'line' || sel.element.type === 'rectangle'">
                                    <div class="cr-rb-props-row">
                                        <div><label>Pen Width</label><input type="number" step="0.5" class="form-control" x-model.number="sel.element.penWidth"></div>
                                        <div>
                                            <label>Pen Style</label>
                                            <select class="form-select" x-model="sel.element.penStyle">
                                                <option>Solid</option><option>Dashed</option><option>Dotted</option><option>Double</option>
                                            </select>
                                        </div>
                                        <div><label>Pen Color</label><input type="color" class="form-control" x-model="sel.element.penColor"></div>
                                    </div>
                                </div>

                                <div x-show="sel.element.type === 'image'">
                                    <div class="cr-rb-props-row"><div><label>Image Expression (path)</label><input class="form-control" x-model="sel.element.expression" placeholder='"/path/to/image.png"'></div></div>
                                    <div class="cr-rb-props-row">
                                        <div>
                                            <label>Scale Image</label>
                                            <select class="form-select" x-model="sel.element.scaleImage">
                                                <option>RetainShape</option><option>FillFrame</option><option>Clip</option><option>RealHeight</option><option>RealSize</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="cr-rb-props-row">
                                    <div><label>Fore Color</label><input class="form-control" x-model="sel.element.forecolor" placeholder="#000000"></div>
                                    <div><label>Back Color</label><input class="form-control" x-model="sel.element.backcolor" placeholder="#FFFFFF"></div>
                                </div>

                                <div class="cr-rb-props-row" style="margin-top:10px;">
                                    <div><button type="button" class="btn btn-sm btn-outline-secondary w-100" x-on:click="duplicateElement()">Duplicate</button></div>
                                    <div><button type="button" class="btn btn-sm btn-outline-danger w-100" x-on:click="deleteElement()">Delete</button></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate output modal -->
    <template x-if="showOutput">
        <div class="cr-rb-modal-back" x-on:click.self="showOutput = false">
            <div class="cr-rb-modal">
                <div class="cr-rb-modal-head">
                    Generated JRXML
                    <div>
                        <button type="button" class="btn btn-sm btn-primary" x-on:click="copyOutput()" x-text="copyLabel"></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" x-on:click="showOutput = false">Close</button>
                    </div>
                </div>
                <div class="cr-rb-modal-body">
                    <textarea readonly x-ref="outputArea" x-text="output"></textarea>
                </div>
            </div>
        </div>
    </template>

    <!-- Load modal -->
    <template x-if="showLoad">
        <div class="cr-rb-modal-back" x-on:click.self="showLoad = false">
            <div class="cr-rb-modal">
                <div class="cr-rb-modal-head">
                    Load JRXML
                    <div>
                        <button type="button" class="btn btn-sm btn-primary" x-on:click="loadFromText()">Load</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" x-on:click="showLoad = false">Close</button>
                    </div>
                </div>
                <div class="cr-rb-modal-body">
                    <div style="margin-bottom:8px;">
                        <input type="file" accept=".jrxml,.xml,text/xml" x-on:change="loadFromFile($event)">
                        <span class="cr-rb-muted">or paste the jrxml content below</span>
                    </div>
                    <textarea x-model="loadText" placeholder="&lt;jasperReport ...&gt;"></textarea>
                    <div class="text-danger" x-text="loadError" x-show="loadError"></div>
                </div>
            </div>
        </div>
    </template>
</div>

@CAppPushScript
<script>
function crReportBuilder() {
    var uid = 1;
    var bandDefs = [
        {type: 'title', label: 'Title'},
        {type: 'pageHeader', label: 'Page Header'},
        {type: 'columnHeader', label: 'Column Header'},
        {type: 'detail', label: 'Detail'},
        {type: 'columnFooter', label: 'Column Footer'},
        {type: 'pageFooter', label: 'Page Footer'},
        {type: 'summary', label: 'Summary'}
    ];
    var bands = {};
    bandDefs.forEach(function(def) {
        bands[def.type] = {enabled: false, height: 30, elements: []};
    });
    bands.pageHeader.enabled = true;
    bands.detail.enabled = true;
    bands.detail.height = 20;

    return {
        report: {name: 'report', pageWidth: 595, pageHeight: 842, leftMargin: 20, rightMargin: 20, topMargin: 20, bottomMargin: 20},
        bandDefs: bandDefs,
        bands: bands,
        groups: [],
        variables: [],
        palette: [
            {type: 'staticText', label: 'Static Text', icon: 'ti-text'},
            {type: 'textField', label: 'Text Field', icon: 'ti-smallcap'},
            {type: 'line', label: 'Line', icon: 'ti-minus'},
            {type: 'rectangle', label: 'Rectangle', icon: 'ti-layout-width-full'},
            {type: 'image', label: 'Image', icon: 'ti-image'}
        ],
        armedTool: null,
        dragTool: null,
        sel: {kind: 'report', band: null, element: null, group: null, variable: null},
        drag: null,
        output: '',
        showOutput: false,
        showLoad: false,
        loadText: '',
        loadError: '',
        copyLabel: 'Copy',

        init: function() {
            var self = this;
            window.addEventListener('mousemove', function(e) { self.onDragMove(e); });
            window.addEventListener('mouseup', function() { self.drag = null; });
        },

        /* ---------- selection ---------- */
        selectReport: function() { this.sel = {kind: 'report', band: null, element: null, group: null, variable: null}; },
        selectBand: function(row) { this.sel = {kind: 'band', band: row.band, element: null, group: null, variable: null}; },
        selectElement: function(band, el) { this.sel = {kind: 'element', band: band, element: el, group: null, variable: null}; },
        selectGroup: function(g) { this.sel = {kind: 'group', band: null, element: null, group: g, variable: null}; },
        selectVariable: function(v) { this.sel = {kind: 'variable', band: null, element: null, group: null, variable: v}; },
        propsTitle: function() {
            var map = {report: 'Report Properties', band: 'Band Properties', element: 'Element Properties', group: 'Group Properties', variable: 'Variable Properties'};
            return map[this.sel.kind] || 'Properties';
        },

        /* ---------- layout helpers ---------- */
        contentWidth: function() {
            return Math.max(50, this.report.pageWidth - this.report.leftMargin - this.report.rightMargin);
        },
        applyPaper: function(paper) {
            var sizes = {A4: [595, 842], A4L: [842, 595], LETTER: [612, 792], LEGAL: [612, 1008]};
            if (sizes[paper]) {
                this.report.pageWidth = sizes[paper][0];
                this.report.pageHeight = sizes[paper][1];
            }
        },
        visibleBands: function() {
            var rows = [];
            var self = this;
            ['title', 'pageHeader', 'columnHeader'].forEach(function(t) {
                if (self.bands[t].enabled) {
                    rows.push({key: t, label: self.labelOf(t), band: self.bands[t]});
                }
            });
            this.groups.forEach(function(g) {
                if (g.header.enabled) {
                    rows.push({key: 'gh-' + g.id, label: 'Group Header [' + g.name + ']', band: g.header});
                }
            });
            if (this.bands.detail.enabled) {
                rows.push({key: 'detail', label: 'Detail', band: this.bands.detail});
            }
            this.groups.slice().reverse().forEach(function(g) {
                if (g.footer.enabled) {
                    rows.push({key: 'gf-' + g.id, label: 'Group Footer [' + g.name + ']', band: g.footer});
                }
            });
            ['columnFooter', 'pageFooter', 'summary'].forEach(function(t) {
                if (self.bands[t].enabled) {
                    rows.push({key: t, label: self.labelOf(t), band: self.bands[t]});
                }
            });
            return rows;
        },
        labelOf: function(type) {
            var found = this.bandDefs.filter(function(d) { return d.type === type; })[0];
            return found ? found.label : type;
        },

        /* ---------- groups & variables ---------- */
        addGroup: function() {
            var g = {
                id: uid++,
                name: 'group' + (this.groups.length + 1),
                expression: '$F{field}',
                reprint: false,
                header: {enabled: true, height: 30, elements: []},
                footer: {enabled: true, height: 24, elements: []}
            };
            this.groups.push(g);
            this.selectGroup(g);
        },
        removeGroup: function(g) {
            this.groups = this.groups.filter(function(x) { return x !== g; });
            if (this.sel.group === g) {
                this.selectReport();
            }
        },
        addVariable: function() {
            var v = {
                id: uid++,
                name: 'variable' + (this.variables.length + 1),
                varClass: 'java.lang.Double',
                calculation: 'Sum',
                expression: '$F{field}',
                initialValue: '',
                resetType: 'Report',
                resetGroup: ''
            };
            this.variables.push(v);
            this.selectVariable(v);
        },
        removeVariable: function(v) {
            this.variables = this.variables.filter(function(x) { return x !== v; });
            if (this.sel.variable === v) {
                this.selectReport();
            }
        },

        /* ---------- elements ---------- */
        newElement: function(type, x, y) {
            var el = {
                id: uid++, type: type,
                x: x, y: y, width: 150, height: 20,
                forecolor: '', backcolor: '',
                text: 'Static Text', expression: '$F{field}', pattern: '', stretch: false,
                fontSize: 10, bold: false, italic: false, underline: false,
                hAlign: 'Left', vAlign: 'Top',
                borders: {top: false, right: false, bottom: false, left: false},
                borderWidth: 1, borderColor: '#000000', borderStyle: 'Solid',
                penWidth: 1, penStyle: 'Solid', penColor: '#000000',
                scaleImage: 'RetainShape'
            };
            if (type === 'line') {
                el.height = 1;
                el.penWidth = 0.5;
            }
            if (type === 'rectangle') {
                el.width = 100;
                el.height = 50;
            }
            if (type === 'image') {
                el.width = 80;
                el.height = 60;
                el.expression = '';
            }
            return el;
        },
        paletteDragStart: function(evt, type) {
            this.dragTool = type;
            evt.dataTransfer.effectAllowed = 'copy';
            evt.dataTransfer.setData('text/plain', type);
        },
        paletteDrop: function(evt, band) {
            var type = this.dragTool || evt.dataTransfer.getData('text/plain');
            this.dragTool = null;
            if (!type) {
                return;
            }
            this.addElementAt(band, type, evt);
        },
        placeElement: function(evt, band) {
            if (!this.armedTool) {
                this.selectBandByObject(band);
                return;
            }
            var type = this.armedTool;
            this.armedTool = null;
            this.addElementAt(band, type, evt);
        },
        addElementAt: function(band, type, evt) {
            var rect = evt.currentTarget.getBoundingClientRect();
            var x = this.snap(evt.clientX - rect.left);
            var y = this.snap(evt.clientY - rect.top);
            var el = this.newElement(type, x, y);
            el.x = Math.max(0, Math.min(el.x, this.contentWidth() - el.width));
            el.y = Math.max(0, Math.min(el.y, Math.max(0, band.height - el.height)));
            band.elements.push(el);
            this.selectElement(band, el);
        },
        selectBandByObject: function(band) {
            var row = this.visibleBands().filter(function(r) { return r.band === band; })[0];
            if (row) {
                this.selectBand(row);
            }
        },
        deleteElement: function() {
            if (this.sel.kind === 'element' && this.sel.band && this.sel.element) {
                var el = this.sel.element;
                this.sel.band.elements = this.sel.band.elements.filter(function(x) { return x !== el; });
                this.selectReport();
            }
        },
        duplicateElement: function() {
            if (this.sel.kind === 'element' && this.sel.band && this.sel.element) {
                var copy = JSON.parse(JSON.stringify(this.sel.element));
                copy.id = uid++;
                copy.x = copy.x + 10;
                copy.y = copy.y + 5;
                this.sel.band.elements.push(copy);
                this.selectElement(this.sel.band, copy);
            }
        },
        snap: function(v) { return Math.round(v / 5) * 5; },

        startDrag: function(evt, band, el, mode) {
            this.selectElement(band, el);
            this.drag = {
                band: band, el: el, mode: mode,
                startX: evt.clientX, startY: evt.clientY,
                origX: el.x, origY: el.y, origW: el.width, origH: el.height
            };
        },
        onDragMove: function(evt) {
            if (!this.drag) {
                return;
            }
            var d = this.drag;
            var dx = evt.clientX - d.startX;
            var dy = evt.clientY - d.startY;
            if (d.mode === 'move') {
                d.el.x = Math.max(0, Math.min(this.snap(d.origX + dx), this.contentWidth() - d.el.width));
                d.el.y = Math.max(0, Math.min(this.snap(d.origY + dy), Math.max(0, d.band.height - d.el.height)));
            } else {
                d.el.width = Math.max(5, this.snap(d.origW + dx));
                d.el.height = Math.max(1, this.snap(d.origH + dy));
            }
        },

        elStyle: function(el) {
            var css = 'left:' + el.x + 'px; top:' + el.y + 'px; width:' + el.width + 'px; height:' + el.height + 'px;';
            if (el.type === 'line') {
                css += 'background:' + (el.penColor || '#000') + ';';
                return css;
            }
            if (el.type === 'rectangle') {
                css += 'border:' + Math.max(1, el.penWidth) + 'px solid ' + (el.penColor || '#000') + ';';
                if (el.backcolor) {
                    css += 'background:' + el.backcolor + ';';
                }
                return css;
            }
            if (el.type === 'image') {
                css += 'background:#f0f3f6; border:1px dotted #9db8d2; color:#7a8894; font-size:10px; display:flex; align-items:center; justify-content:center;';
                return css;
            }
            css += 'font-size:' + el.fontSize + 'px;';
            css += 'font-weight:' + (el.bold ? 'bold' : 'normal') + ';';
            css += 'font-style:' + (el.italic ? 'italic' : 'normal') + ';';
            if (el.underline) {
                css += 'text-decoration:underline;';
            }
            var alignMap = {Left: 'left', Center: 'center', Right: 'right', Justified: 'justify'};
            css += 'text-align:' + (alignMap[el.hAlign] || 'left') + ';';
            var flexMap = {Top: 'flex-start', Middle: 'center', Bottom: 'flex-end'};
            css += 'display:flex; flex-direction:column; justify-content:' + (flexMap[el.vAlign] || 'flex-start') + ';';
            if (el.forecolor) {
                css += 'color:' + el.forecolor + ';';
            }
            if (el.backcolor) {
                css += 'background:' + el.backcolor + ';';
            }
            ['top', 'right', 'bottom', 'left'].forEach(function(side) {
                if (el.borders[side]) {
                    css += 'border-' + side + ':' + Math.max(1, el.borderWidth) + 'px solid ' + (el.borderColor || '#000') + ';';
                }
            });
            if (el.type === 'textField') {
                css += 'color:' + (el.forecolor || '#3c6499') + ';';
            }
            return css;
        },
        elDisplayText: function(el) {
            if (el.type === 'staticText') {
                return el.text;
            }
            if (el.type === 'textField') {
                return el.expression;
            }
            if (el.type === 'image') {
                return el.expression || 'image';
            }
            return '';
        },

        /* ---------- generate jrxml ---------- */
        esc: function(s) {
            return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        },
        cdata: function(s) {
            return '<![CDATA[' + String(s == null ? '' : s).replace(/]]>/g, ']]]]><![CDATA[>') + ']]>';
        },
        penXml: function(tag, width, style, color) {
            return '<' + tag + ' lineWidth="' + width + '" lineStyle="' + style + '" lineColor="' + color + '"/>';
        },
        reportElementXml: function(el) {
            var attrs = ' x="' + el.x + '" y="' + el.y + '" width="' + el.width + '" height="' + el.height + '"';
            if (el.forecolor) {
                attrs += ' forecolor="' + this.esc(el.forecolor) + '"';
            }
            if (el.backcolor) {
                attrs += ' backcolor="' + this.esc(el.backcolor) + '" mode="Opaque"';
            }
            return '<reportElement' + attrs + '/>';
        },
        textElementXml: function(el) {
            var xml = '<textElement textAlignment="' + el.hAlign + '" verticalAlignment="' + el.vAlign + '">';
            xml += '<font size="' + el.fontSize + '"';
            if (el.bold) {
                xml += ' isBold="true"';
            }
            if (el.italic) {
                xml += ' isItalic="true"';
            }
            if (el.underline) {
                xml += ' isUnderline="true"';
            }
            xml += '/></textElement>';
            return xml;
        },
        boxXml: function(el) {
            var b = el.borders;
            if (!b.top && !b.right && !b.bottom && !b.left) {
                return '';
            }
            var xml = '<box>';
            var self = this;
            [['top', 'topPen'], ['left', 'leftPen'], ['bottom', 'bottomPen'], ['right', 'rightPen']].forEach(function(pair) {
                if (b[pair[0]]) {
                    xml += self.penXml(pair[1], el.borderWidth, el.borderStyle, el.borderColor);
                }
            });
            xml += '</box>';
            return xml;
        },
        elementXml: function(el, pad) {
            var xml = '';
            if (el.type === 'staticText') {
                xml = pad + '<staticText>\n'
                    + pad + '    ' + this.reportElementXml(el) + '\n'
                    + (this.boxXml(el) ? pad + '    ' + this.boxXml(el) + '\n' : '')
                    + pad + '    ' + this.textElementXml(el) + '\n'
                    + pad + '    <text>' + this.cdata(el.text) + '</text>\n'
                    + pad + '</staticText>';
            } else if (el.type === 'textField') {
                var open = '<textField';
                if (el.pattern) {
                    open += ' pattern="' + this.esc(el.pattern) + '"';
                }
                if (el.stretch) {
                    open += ' textAdjust="StretchHeight"';
                }
                open += '>';
                xml = pad + open + '\n'
                    + pad + '    ' + this.reportElementXml(el) + '\n'
                    + (this.boxXml(el) ? pad + '    ' + this.boxXml(el) + '\n' : '')
                    + pad + '    ' + this.textElementXml(el) + '\n'
                    + pad + '    <textFieldExpression>' + this.cdata(el.expression) + '</textFieldExpression>\n'
                    + pad + '</textField>';
            } else if (el.type === 'line' || el.type === 'rectangle') {
                xml = pad + '<' + el.type + '>\n'
                    + pad + '    ' + this.reportElementXml(el) + '\n'
                    + pad + '    <graphicElement>' + this.penXml('pen', el.penWidth, el.penStyle, el.penColor) + '</graphicElement>\n'
                    + pad + '</' + el.type + '>';
            } else if (el.type === 'image') {
                xml = pad + '<image scaleImage="' + el.scaleImage + '">\n'
                    + pad + '    ' + this.reportElementXml(el) + '\n'
                    + pad + '    <imageExpression>' + this.cdata(el.expression) + '</imageExpression>\n'
                    + pad + '</image>';
            }
            return xml;
        },
        bandXml: function(tag, band, pad) {
            var self = this;
            var inner = band.elements.map(function(el) { return self.elementXml(el, pad + '        '); }).join('\n');
            return pad + '<' + tag + '>\n'
                + pad + '    <band height="' + band.height + '" splitType="Stretch">\n'
                + (inner ? inner + '\n' : '')
                + pad + '    </band>\n'
                + pad + '</' + tag + '>';
        },
        variableXml: function(v) {
            var xml = '    <variable name="' + this.esc(v.name) + '" class="' + v.varClass + '" calculation="' + v.calculation + '">\n';
            xml += '        <variableExpression>' + this.cdata(v.expression) + '</variableExpression>\n';
            if (v.initialValue) {
                xml += '        <initialValueExpression>' + this.cdata(v.initialValue) + '</initialValueExpression>\n';
            }
            xml += '        <resetType>' + v.resetType + '</resetType>\n';
            if (v.resetType === 'Group' && v.resetGroup) {
                xml += '        <resetGroup>' + this.esc(v.resetGroup) + '</resetGroup>\n';
            }
            xml += '    </variable>';
            return xml;
        },
        groupXml: function(g) {
            var xml = '    <group name="' + this.esc(g.name) + '" isReprintHeaderOnEachPage="' + (g.reprint ? 'true' : 'false') + '">\n';
            xml += '        <groupExpression>' + this.cdata(g.expression) + '</groupExpression>\n';
            if (g.header.enabled) {
                xml += this.bandXml('groupHeader', g.header, '        ') + '\n';
            }
            if (g.footer.enabled) {
                xml += this.bandXml('groupFooter', g.footer, '        ') + '\n';
            }
            xml += '    </group>';
            return xml;
        },
        generate: function() {
            var r = this.report;
            var self = this;
            var lines = [];
            lines.push('<?xml version="1.0" encoding="UTF-8"?>');
            lines.push('<jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports"'
                + ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
                + ' xsi:schemaLocation="http://jasperreports.sourceforge.net/jasperreports http://jasperreports.sourceforge.net/xsd/jasperreport.xsd"'
                + ' name="' + this.esc(r.name) + '"'
                + ' pageWidth="' + r.pageWidth + '" pageHeight="' + r.pageHeight + '"'
                + ' columnWidth="' + this.contentWidth() + '"'
                + ' leftMargin="' + r.leftMargin + '" rightMargin="' + r.rightMargin + '"'
                + ' topMargin="' + r.topMargin + '" bottomMargin="' + r.bottomMargin + '">');
            this.variables.forEach(function(v) { lines.push(self.variableXml(v)); });
            this.groups.forEach(function(g) { lines.push(self.groupXml(g)); });
            ['title', 'pageHeader', 'columnHeader', 'detail', 'columnFooter', 'pageFooter', 'summary'].forEach(function(t) {
                if (self.bands[t].enabled) {
                    lines.push(self.bandXml(t, self.bands[t], '    '));
                }
            });
            lines.push('</jasperReport>');
            return lines.join('\n');
        },
        openOutput: function() {
            this.output = this.generate();
            this.copyLabel = 'Copy';
            this.showOutput = true;
        },
        copyOutput: function() {
            var self = this;
            var done = function() { self.copyLabel = 'Copied!'; };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(this.output).then(done);
            } else {
                var area = this.$refs.outputArea;
                area.select();
                document.execCommand('copy');
                done();
            }
        },

        /* ---------- load jrxml ---------- */
        openLoad: function() {
            this.loadText = '';
            this.loadError = '';
            this.showLoad = true;
        },
        loadFromFile: function(evt) {
            var self = this;
            var file = evt.target.files[0];
            if (!file) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function() { self.loadText = reader.result; };
            reader.readAsText(file);
        },
        loadFromText: function() {
            this.loadError = '';
            try {
                this.parseJrxml(this.loadText);
                this.showLoad = false;
                this.selectReport();
            } catch (e) {
                this.loadError = 'Parse error: ' + e.message;
            }
        },
        parseJrxml: function(text) {
            var doc = new DOMParser().parseFromString(text, 'text/xml');
            var root = doc.documentElement;
            if (!root || root.nodeName !== 'jasperReport' || doc.getElementsByTagName('parsererror').length) {
                throw new Error('invalid jrxml document');
            }
            var self = this;
            var num = function(node, name, def) {
                var v = node.getAttribute(name);
                return v === null || v === '' ? def : parseFloat(v);
            };
            this.report.name = root.getAttribute('name') || 'report';
            this.report.pageWidth = num(root, 'pageWidth', 595);
            this.report.pageHeight = num(root, 'pageHeight', 842);
            this.report.leftMargin = num(root, 'leftMargin', 20);
            this.report.rightMargin = num(root, 'rightMargin', 20);
            this.report.topMargin = num(root, 'topMargin', 20);
            this.report.bottomMargin = num(root, 'bottomMargin', 20);

            this.bandDefs.forEach(function(def) {
                self.bands[def.type] = {enabled: false, height: 30, elements: []};
            });
            this.groups = [];
            this.variables = [];

            var children = root.children;
            for (var i = 0; i < children.length; i++) {
                var node = children[i];
                var tag = node.nodeName;
                if (this.bands[tag] !== undefined) {
                    this.parseBandInto(node, this.bands[tag]);
                } else if (tag === 'variable') {
                    this.variables.push(this.parseVariable(node));
                } else if (tag === 'group') {
                    this.groups.push(this.parseGroup(node));
                }
            }
        },
        childByTag: function(node, tag) {
            for (var i = 0; i < node.children.length; i++) {
                if (node.children[i].nodeName === tag) {
                    return node.children[i];
                }
            }
            return null;
        },
        textOf: function(node, tag) {
            var child = this.childByTag(node, tag);
            return child ? child.textContent : '';
        },
        parseVariable: function(node) {
            return {
                id: uid++,
                name: node.getAttribute('name') || 'variable',
                varClass: node.getAttribute('class') || 'java.lang.Double',
                calculation: node.getAttribute('calculation') || 'Nothing',
                expression: this.textOf(node, 'variableExpression'),
                initialValue: this.textOf(node, 'initialValueExpression'),
                resetType: this.textOf(node, 'resetType') || 'Report',
                resetGroup: this.textOf(node, 'resetGroup')
            };
        },
        parseGroup: function(node) {
            var g = {
                id: uid++,
                name: node.getAttribute('name') || 'group',
                expression: this.textOf(node, 'groupExpression'),
                reprint: node.getAttribute('isReprintHeaderOnEachPage') === 'true',
                header: {enabled: false, height: 30, elements: []},
                footer: {enabled: false, height: 24, elements: []}
            };
            var header = this.childByTag(node, 'groupHeader');
            if (header) {
                g.header.enabled = true;
                this.parseBandInto(header, g.header);
            }
            var footer = this.childByTag(node, 'groupFooter');
            if (footer) {
                g.footer.enabled = true;
                this.parseBandInto(footer, g.footer);
            }
            return g;
        },
        parseBandInto: function(node, band) {
            var bandNode = this.childByTag(node, 'band');
            band.enabled = true;
            if (!bandNode) {
                return;
            }
            band.height = parseFloat(bandNode.getAttribute('height') || '30');
            band.elements = [];
            for (var i = 0; i < bandNode.children.length; i++) {
                var child = bandNode.children[i];
                var el = this.parseElement(child);
                if (el) {
                    band.elements.push(el);
                }
            }
        },
        parseElement: function(node) {
            var tag = node.nodeName;
            var known = ['staticText', 'textField', 'line', 'rectangle', 'image'];
            if (known.indexOf(tag) === -1) {
                return null;
            }
            var el = this.newElement(tag, 0, 0);
            el.text = '';
            el.expression = '';
            var re = this.childByTag(node, 'reportElement');
            if (re) {
                el.x = parseFloat(re.getAttribute('x') || '0');
                el.y = parseFloat(re.getAttribute('y') || '0');
                el.width = parseFloat(re.getAttribute('width') || '100');
                el.height = parseFloat(re.getAttribute('height') || '20');
                el.forecolor = re.getAttribute('forecolor') || '';
                el.backcolor = re.getAttribute('backcolor') || '';
            }
            var te = this.childByTag(node, 'textElement');
            if (te) {
                var cap = function(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1).toLowerCase() : s; };
                if (te.getAttribute('textAlignment')) {
                    el.hAlign = cap(te.getAttribute('textAlignment'));
                }
                if (te.getAttribute('verticalAlignment')) {
                    el.vAlign = cap(te.getAttribute('verticalAlignment'));
                }
                var font = this.childByTag(te, 'font');
                if (font) {
                    el.fontSize = parseFloat(font.getAttribute('size') || '10');
                    el.bold = font.getAttribute('isBold') === 'true';
                    el.italic = font.getAttribute('isItalic') === 'true';
                    el.underline = font.getAttribute('isUnderline') === 'true';
                }
            }
            var box = this.childByTag(node, 'box');
            if (box) {
                var self = this;
                [['topPen', 'top'], ['rightPen', 'right'], ['bottomPen', 'bottom'], ['leftPen', 'left']].forEach(function(pair) {
                    var pen = self.childByTag(box, pair[0]);
                    if (pen && parseFloat(pen.getAttribute('lineWidth') || '0') > 0) {
                        el.borders[pair[1]] = true;
                        el.borderWidth = parseFloat(pen.getAttribute('lineWidth'));
                        el.borderColor = pen.getAttribute('lineColor') || '#000000';
                        el.borderStyle = pen.getAttribute('lineStyle') || 'Solid';
                    }
                });
            }
            if (tag === 'staticText') {
                el.text = this.textOf(node, 'text');
            }
            if (tag === 'textField') {
                el.expression = this.textOf(node, 'textFieldExpression');
                el.pattern = node.getAttribute('pattern') || this.textOf(node, 'pattern') || '';
                el.stretch = node.getAttribute('textAdjust') === 'StretchHeight' || node.getAttribute('isStretchWithOverflow') === 'true';
            }
            if (tag === 'line' || tag === 'rectangle') {
                var ge = this.childByTag(node, 'graphicElement');
                var pen = ge ? this.childByTag(ge, 'pen') : null;
                if (pen) {
                    el.penWidth = parseFloat(pen.getAttribute('lineWidth') || '1');
                    el.penStyle = pen.getAttribute('lineStyle') || 'Solid';
                    el.penColor = pen.getAttribute('lineColor') || '#000000';
                }
            }
            if (tag === 'image') {
                el.expression = this.textOf(node, 'imageExpression');
                el.scaleImage = node.getAttribute('scaleImage') || 'RetainShape';
            }
            return el;
        },

        /* ---------- sample ---------- */
        loadSample: function() {
            var sample = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports" name="sample" pageWidth="595" pageHeight="842" columnWidth="555" leftMargin="20" rightMargin="20" topMargin="20" bottomMargin="20">',
                '    <variable name="grandTotal" class="java.lang.Double" calculation="Sum">',
                '        <variableExpression><![CDATA[$F{subtotal}]]></variableExpression>',
                '        <resetType>Report</resetType>',
                '    </variable>',
                '    <pageHeader>',
                '        <band height="30" splitType="Stretch">',
                '            <staticText>',
                '                <reportElement x="0" y="0" width="555" height="30" forecolor="#FF0000"/>',
                '                <textElement textAlignment="Center" verticalAlignment="Middle"><font size="16" isBold="true"/></textElement>',
                '                <text><![CDATA[Sample Report]]></text>',
                '            </staticText>',
                '        </band>',
                '    </pageHeader>',
                '    <columnHeader>',
                '        <band height="20" splitType="Stretch">',
                '            <textField>',
                '                <reportElement x="0" y="0" width="275" height="20"/>',
                '                <box><topPen lineWidth="1" lineStyle="Solid" lineColor="#000000"/><bottomPen lineWidth="1" lineStyle="Solid" lineColor="#000000"/></box>',
                '                <textElement textAlignment="Left" verticalAlignment="Middle"><font size="10" isBold="true"/></textElement>',
                '                <textFieldExpression><![CDATA["Name"]]></textFieldExpression>',
                '            </textField>',
                '            <textField>',
                '                <reportElement x="275" y="0" width="280" height="20"/>',
                '                <box><topPen lineWidth="1" lineStyle="Solid" lineColor="#000000"/><bottomPen lineWidth="1" lineStyle="Solid" lineColor="#000000"/></box>',
                '                <textElement textAlignment="Right" verticalAlignment="Middle"><font size="10" isBold="true"/></textElement>',
                '                <textFieldExpression><![CDATA["Subtotal"]]></textFieldExpression>',
                '            </textField>',
                '        </band>',
                '    </columnHeader>',
                '    <detail>',
                '        <band height="18" splitType="Stretch">',
                '            <textField>',
                '                <reportElement x="0" y="0" width="275" height="18"/>',
                '                <textElement textAlignment="Left" verticalAlignment="Middle"><font size="10"/></textElement>',
                '                <textFieldExpression><![CDATA[$F{name}]]></textFieldExpression>',
                '            </textField>',
                '            <textField pattern="#,##0">',
                '                <reportElement x="275" y="0" width="280" height="18"/>',
                '                <textElement textAlignment="Right" verticalAlignment="Middle"><font size="10"/></textElement>',
                '                <textFieldExpression><![CDATA[$F{subtotal}]]></textFieldExpression>',
                '            </textField>',
                '        </band>',
                '    </detail>',
                '    <summary>',
                '        <band height="24" splitType="Stretch">',
                '            <line><reportElement x="0" y="0" width="555" height="1"/><graphicElement><pen lineWidth="1" lineStyle="Solid" lineColor="#000000"/></graphicElement></line>',
                '            <textField>',
                '                <reportElement x="275" y="4" width="280" height="18"/>',
                '                <textElement textAlignment="Right" verticalAlignment="Middle"><font size="11" isBold="true"/></textElement>',
                '                <textFieldExpression><![CDATA["Grand Total : " + $V{grandTotal}]]></textFieldExpression>',
                '            </textField>',
                '        </band>',
                '    </summary>',
                '    <pageFooter>',
                '        <band height="20" splitType="Stretch">',
                '            <textField>',
                '                <reportElement x="455" y="0" width="100" height="20"/>',
                '                <textElement textAlignment="Right" verticalAlignment="Middle"><font size="8"/></textElement>',
                '                <textFieldExpression><![CDATA["Page " + $V{PAGE_NUMBER}]]></textFieldExpression>',
                '            </textField>',
                '        </band>',
                '    </pageFooter>',
                '</jasperReport>'
            ].join('\n');
            this.parseJrxml(sample);
            this.selectReport();
        }
    };
}
</script>
@CAppEndPushScript
