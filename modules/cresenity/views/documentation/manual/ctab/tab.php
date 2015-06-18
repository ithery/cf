<div id="tab">
    <div class="page-header">
        <h4>Membuat tab</h4>
    </div>
    <pre class="prettyprint linenums">$tab = CTabList::factory('nama_table');
$tabs = $app->add_tab_list();
$tabs->add_tab('tab_1')->set_label('TAB 1')->add_field()->set_label('TEST 1');</pre>

    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('tab/one-tab.png'); ?>"></img>

    <div class="page-header">
        <h4>Set Label</h4>
    </div>
    <p>membuat label nama pada tab</p>
    <pre class="prettyprint linenums">$tabs->add_tab('tab_1')->set_label(' ... ');</pre>

    <div class="page-header">
        <h4>Add Widget</h4>
    </div>
    <p>Membuat widget</p>
    <pre class="prettyprint linenums">$tabs->add_tab('tab_1')->add_widget();</pre>
    <img src="<?php echo c::manimgurl('tab/widget.png'); ?>"></img>

    <div class="page-header">
        <h4>Set Ajax</h4>
    </div>
    <pre class="prettyprint linenums">$tabs = $app->add_tab_list()->set_ajax(true);</pre>
    <p>Isi method dengan True atau False</p>
    
    <div class="page-header">
        <h4>Add Listener, Handler, Set Target</h4>
    </div>
    <ul>
        <li>add listener digunakan untuk perintah untuk menjalakan method, isiannya berupa event seperti click, onchange, blur, dll </li>
        <li>add handler digunakan untuk menjalakan perintah yang dikirim dari method add_listener</li>
        <li>set target sebagai pemanggilan id, class atau selector yang akan dijalankan</li>
    </ul>
    
    <pre class="prettyprint linenums">$tab_4 = $tabs->add_tab('tab_4')->set_label('TAB 4');
$reload_content = $tab_4->add_action()->set_label('Reload')->add_listener('click')->add_handler('reload')->set_target('reload_div')->content();
$reload_content->add_widget();
$tab_4->add_div('reload_div');</pre>
    <img src="<?php echo c::manimgurl('tab/listener.png'); ?>"></img>
</div>