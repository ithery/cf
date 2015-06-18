<div id="nestable">
    <div class="page-header">
        <h4>Membuat Hirarki database</h4>
    </div>
    <p>set_data_from_treedb digunakan ketika data mempunyai urutan struktur database yang memiliki parent dan child</p>
    
    <pre class="prettyprint linenums">$tree = CTreeDB::factory('nama_table');</pre>
    
    <div class="page-header">
    <h4>Menampilkan Table Hirarki</h4>
    </div>
    <pre class="prettyprint linenums">$widget = $app->add_widget()->set_title(clang::__('judul'));
$nestable = $widget->add_nestable();
$nestable->set_data_from_treedb($tree)->set_id_key('$id')->set_value_key('nama_field_database')->set_input('nama_field');</pre>
    <p>Set Id key memanggil id database</p>
    <p>Set Value Key menampilkan nama database</p>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('nested/applyjs.png'); ?>"></img>
    
    
    <div class="page-header">
        <h4>Fungsi Row Action</h4>
    </div>
    <pre class="prettyprint linenums">$actedit = $nestable->add_row_action('[edit, add, delete, info, dll]');</pre>
    
    <div class="page-header">
        <h4>Fungsi Set ApplyJS</h4>
    </div>
    <p>Mengubah struktur hirarki secara drag and drop</p>
    <pre class="prettyprint linenums">$nestable->set_applyjs(false);</pre>
    <p>Isi method dengan True atau False</p>
</div>