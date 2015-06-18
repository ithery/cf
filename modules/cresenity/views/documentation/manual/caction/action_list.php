<div id="action_list">
    <div class="page-header">
        <h4>Membuat Action List</h4>
    </div>
    <pre class="prettyprint linenums">$actions = CActionList::factory();</pre>

    <div class="page-header">
        <h4>Set Style</h4>
    </div>
    <pre class="prettyprint linenums">$actions->set_style($type);</pre>
    <p>$type bisa diisi dengan string control, yang tersedia :</p>
    <ul>
        <li>form-action</li>
        <li>btn-group</li>
        <li>btn-icon-group</li>
        <li>btn-list</li>
        <li>icon-segment</li>
        <li>btn-dropdown</li>
        <li>widget-action</li>
    </ul>
</div>