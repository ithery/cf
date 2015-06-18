<div id="add_js">
    <div class="page-header">
        <h1>add_js</h1>
    </div>

    <p>digunakan untuk meload fungsi javascript dari halaman controller</p>

    <h2>Example</h2>
    <h4>Fungsi add_js</h4>
    <pre class="prettyprint linenums">
public function add_js($js) {
    $this->custom_js.= $js;
    return $this;
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">
$vjs = CView::factory('nama_folder/js');
$js = $vjs->render();

$app->add_js($js);</pre>
</div>