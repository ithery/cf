<div id="escape">
    <div class="page-header">
        <h1>escape</h1>
    </div>

    <p>digunakan untuk mengambil value dari variable</p>

    <h2>Example</h2>
    <h4>Fungsi escape</h4>
    <pre class="prettyprint linenums">
public function escape($value) {
    return $this->driver->escape($value);
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">
$org_id = "";
if ($org != null) {
    $org_id = $org->org_id;
}
$db = CDatabase::instance();
$q = ' select * from nama_table where org_id=' . $db->escape($org_id);</pre>
</div>