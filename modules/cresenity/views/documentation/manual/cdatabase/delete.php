<div id="delete">
    <div class="page-header">
        <h1>delete</h1>
    </div>

    <p>menghapus data di database </p>

    <h4>Fungsi delete</h4>
    <pre class="prettyprint linenums">
public function delete($table = '', $where = NULL) {
    if ($table == '') {
        if (!isset($this->from[0]))
            throw new CDatabase_Exception('database.must_use_table');

        $table = $this->from[0];
    }
    else {
        $table = $this->config['table_prefix'] . $table;
    }

    if (!is_null($where)) {
        $this->where($where);
    }

    if (count($this->where) < 1)
        throw new CDatabase_Exception('database.must_use_where');

    $sql = $this->driver->delete($table, $this->where);

    $this->reset_write();
    return $this->query($sql);
}</pre>
    <h4>Penggunaan</h4>
    <h2>Example</h2>
    <pre class="prettyprint linenums">
$q = 'delete from nama_table where id = ' . $db->escape($id);
$db->query($q);</pre>
</div>