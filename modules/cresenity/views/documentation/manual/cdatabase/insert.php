<div id="insert">
    <div class="page-header">
        <h1>insert</h1>
    </div>

    <p>membuat data baru</p>

    <h4>Fungsi insert</h4>
    <pre class="prettyprint linenums">
public function insert($table = '', $set = NULL) {
        if (!is_null($set)) {
            $this->set($set);
        }

        if ($this->set == NULL)
            throw new CDatabase_Exception('database.must_use_set');

        if ($table == '') {
            if (!isset($this->from[0]))
                throw new CDatabase_Exception('database.must_use_table');

            $table = $this->from[0];
        }

        // If caching is enabled, clear the cache before inserting
        ($this->config['cache'] === TRUE) and $this->clear_cache();

        $sql = $this->driver->insert($this->config['table_prefix'] . $table, array_keys($this->set), array_values($this->set));

        $this->reset_write();

        return $this->query($sql);
    }
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">
$data = array(
    "nama_field" => $nama_variable,
));
$r = $db->insert("nama_table", $data);</pre>
</div>