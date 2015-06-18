<div id="db">
    <div class="page-header">
        <h4>Database</h4>
    </div>

    <p>koneksi ke database</p>
    <pre class="prettyprint linenums">
$db = CDatabase::instance();</pre>

    <div class="page-header">
        <h4>Escape</h4>
    </div>

    <pre class="prettyprint linenums">
$q = ' select * from nama_table where org_id=' . $db->escape($id);</pre>

    <div class="page-header">
        <h4>Insert</h4>
    </div>

    <pre class="prettyprint linenums">
$data = array(
    "nama_field" => $nama_variable,
));
$r = $db->insert("nama_table", $data);</pre>
    
    <div class="page-header">
        <h4>Update</h4>
    </div>

    <pre class="prettyprint linenums">
$data = array(
    "nama_field" => $nama_variable,
));
$db->update("nama_table", $data, array("id" => $id));</pre>
    
    <div class="page-header">
        <h4>Delete</h4>
    </div>

    <pre class="prettyprint linenums">
$q = 'delete from nama_table where id = ' . $db->escape($id);
$db->query($q);</pre>

</div>