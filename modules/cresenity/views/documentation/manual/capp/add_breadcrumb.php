<div id="add_breadcrumb">
    <div class="page-header">
        <h1>add_breadcrumb</h1>
    </div>

    <p>membuat breadcrumb</p>

    <h4>Fungsi add_breadcrumb</h4>
    <pre class="prettyprint linenums">public function add_breadcrumb($caption, $url) {
    $this->breadcrumb[$caption] = $url;
    return $this;
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">$app->add_breadcrumb(clang::__(" ... "), curl::base() . " ... ");</pre>
</div>