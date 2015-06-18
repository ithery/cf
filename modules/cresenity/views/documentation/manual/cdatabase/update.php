<div id="update">
    <div class="page-header">
        <h1>update</h1>
    </div>
    <p>mengupdate perubahan data ke database</p>

    <h4>Fungsi update </h4>
    <pre class="prettyprint linenums">
public function update($table = '', $set = NULL, $where = NULL) {
    if (is_array($set)) {
        $this->set($set);
    }

    if (!is_null($where)) {
        $this->where($where);
    }

    if ($this->set == FALSE)
        throw new CDatabase_Exception('database.must_use_set');

    if ($table == '') {
        if (!isset($this->from[0]))
            throw new CDatabase_Exception('database.must_use_table');

        $table = $this->from[0];
    }

    $sql = $this->driver->update($this->config['table_prefix'] . $table, $this->set, $this->where);

    $this->reset_write();
    return $this->query($sql);
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">
$data = array(
    "nama_field" => $nama_variable,
));
$db->update("nama_table", $data, array("id" => $id));</pre>
</div>