<div id="store">
    <div class="page-header">
        <h1>store</h1>
    </div>

    <p>membuat store namum sebelumnya harus memiliki org terlebih dahulu</p>

    <h2>Example</h2>
    <h4>Fungsi store</h4>
    <pre class="prettyprint linenums">
public function store() {
    if ($this->_store == null) {
        $store_id = CF::store_id();
        if ($store_id != "") {
            $this->_store = cstore::get($this->org()->org_id, $store_id);
        }
    }
    return $this->_store;
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">$store = $app->store();</pre>
</div>