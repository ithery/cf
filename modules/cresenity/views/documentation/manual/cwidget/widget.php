<div id="widget">
    <div class="page-header">
        <h4>Membuat widget</h4>
    </div>
    <pre class="prettyprint linenums">$widget = CWidget::factory();
$app->add($widget);</pre>
    <p>atau bisa juga ditulis :</p>
    <pre class="prettyprint linenums">$widget = $app->add_widget();</pre>

    <div class="page-header">
        <h4>Membuat Judul</h4>
    </div>
    <pre class="prettyprint linenums">$widget->set_title(clang::__(" ... "));</pre>

    <div class="page-header">
        <h4>Membuat Icon</h4>
    </div>
    <pre class="prettyprint linenums">$widget->set_icon(' ... ');</pre>

    <div class="page-header">
        <h4>Nopadding</h4>
    </div>
    <p>menset frame table tanpa atau menggunakan jarak</p>
    <pre class="prettyprint linenums">$widget->set_nopadding(true);</pre>
    <p>isikan method dengan TRUE atau FALSE</p>     

    <div class="page-header">
        <h4>Membuat action pada header</h4>
    </div>
    <!--<p>menset frame table tanpa atau menggunakan jarak</p>-->
    <pre class="prettyprint linenums">$widget->add_header_action()->set_link(curl::base() . "$url")->set_confirm(true)->set_icon('trash')->set_label(clang::__('Delete'))->add_class('btn-danger');</pre>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('widget/add_header_action.png'); ?>"></img>
</div>