<div class="table-responsive" style="overflow: auto;">
    <table class="table responsive table-striped table-bordered">
        <thead>
            <tr>
                @foreach ($explainer->getHeaderRow() as $col => $infos)
                    <th><a class="a-black" href="#" data-action="showInfos"
                            data-params="@jsonAttr(['infos'=>$infos , 'link'=> $mysqlBaseDocUrl.'#explain_'.$col])">{{ $col }}</a>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($explainer->getRows() as $row)
                <tr>
                    @foreach ($row->getCells() as $cell)
                        <td id="{{ $cell->id }}"">
                            <a class="a-black" href="#" data-action="showInfos" data-params="@jsonAttr(["infos"=>
                                $cell->info, "link" => $mysqlBaseDocUrl])"
                                >
                                @if ($cell->isDanger())
                                    <span class="label label-danger"><span class="glyphicon glyphicon-fire"></span>
                                        <?= $cell->v ?></span>
                                @elseif ($cell->isSuccess())
                                    <span class="label label-success"><span
                                            class="glyphicon glyphicon-thumbs-up"></span> <?= $cell->v ?></span>
                                @elseif ($cell->isWarning())
                                    <span class="label label-warning">{{ $cell->v }}></span>
                                @else
                                    <?= $cell->v ?>
                                @endif
                            </a>
                        </td>
                        <?php endforeach; ?>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="comp_infos" class="alert alert-info">
    <a id="mysql_doc_link" class="pull-right" target="_blank" href="#" class="mysq_doc_link">
        <span class="glyphicon glyphicon-question-sign"></span></a>
    <span id="infos_text"></span>
</div>
@if (count($hints = $explainer->getHints()))
    <hr />
    <label>Hints</label>
    <ol>
        @foreach ($hints as $hint)
            <li>
                {{ $hint }}
            </li>
        @endforeach
    </ol>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var Actions = {
            // Ajout de la zone de contexte des requetes
            addContext: (function(alreadyInclude) {
                return function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (!alreadyInclude) {
                        document.getElementById('context_queries').style.display = 'block';
                        document.getElementById('addContext').classList.add('disabled');
                        alreadyInclude = true;
                    }
                }
            })(false),
            // Les infos sur une donnée de l'explain
            showInfos: (function() {
                return function(e, params) {
                    e.stopPropagation();
                    e.preventDefault();
                    document.getElementById('infos_text').innerHTML = params["infos"];
                    document.getElementById('infos_text').parentNode.style.display = 'block';
                    document.getElementById('mysql_doc_link').setAttribute('href', params[
                        "link"]);
                }
            })(false)
        };

        var actionElements = document.querySelectorAll('[data-action]');
        actionElements.forEach(function(element) {
            element.addEventListener('click', function(e) {
                var fnName = this.getAttribute('data-action');
                if (typeof Actions[fnName] === 'function') {
                    var params = this.getAttribute('data-params');
                    try {
                        params = JSON.parse(params);
                    } catch (e) {}
                    Actions[fnName].apply(null, [e, params]);
                }
            });
        });
    });
</script>
