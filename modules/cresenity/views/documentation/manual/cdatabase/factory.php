<div id="factory">
    <div class="page-header">
        <h1>factory</h1>
    </div>
    <p>Memanggil fungsi table</p>

    <h4>Fungsi Factory </h4>
    <pre class="prettyprint linenums">
public static function factory($id) {
        return new CTable($id);
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">
$table = CTable::factory(' ... ');</pre>
</div>