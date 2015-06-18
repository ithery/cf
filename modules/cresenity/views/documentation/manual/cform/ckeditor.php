<div id="ckeditor">    
    <div class="page-header">
        <h4>CKEditor</h4>
    </div> 
    <pre class="prettyprint linenums">$app->add_widget();
$f = $app->add_form();
$f->add_field()->set_label('Editor')->add_control('editor','ckeditor')->toolbar_full();</pre>
    <p>membuat fungsi editor</p>
    <p>Fungsi Lain:</p>
    <ul>
        <li>toolbar_full()</li>
        <li>toolbar_basic()</li>
        <li>toolbar_standard()</li>
    </ul>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/ckeditor.png'); ?>"></img>
</div>