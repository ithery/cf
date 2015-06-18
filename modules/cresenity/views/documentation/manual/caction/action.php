<div id="action">
    <div class="page-header">
        <h4>membuat action</h4>
    </div>
    <pre class="prettyprint linenums">$action = CAction::factory();</pre>

    <div class="page-header">
        <h4>membuat label</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_label('...');</pre>

    <div class="page-header">
        <h4>membuat icon</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_icon('...');</pre>
    
     <div class="page-header">
        <h4>membuat action confirm</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_confirm(True);</pre>
    <p>Isi method dengan TRUE atau FALSE</p>
    
     <div class="page-header">
        <h4>set type</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_type("submit");</pre>
    
     <div class="page-header">
        <h4>set link</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_link(curl::base() . "$url");</pre>
    <p>atau bisa juga ditulis</p>
    <pre class="prettyprint linenums">$act_next = $action->set_link_target("$url");</pre>
    
    <div class="page-header">
        <h4>Set Submit</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_submit(true);</pre>
    <p>Isi method dengan TRUE atau FALSE</p>
    
    <div class="page-header">
        <h4>set disable</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_disable(true);</pre>
    <p>Isi method dengan TRUE atau FALSE</p>
    
    <div class="page-header">
        <h4>set disable</h4>
    </div>
    <pre class="prettyprint linenums">$act_next = $action->set_disable(true);</pre>
    <p>Isi method dengan TRUE atau FALSE</p>
</div>