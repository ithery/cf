<div id="factory">
    <div class="page-header">
        <h4>Membuat Table</h4>
    </div>
    <pre class="prettyprint linenums">$table = CTable::factory(' ... ');
$app->add($table);</pre>
    <p>Atau bisa juga: </p>
    <pre class="prettyprint linenums">$table = $app->add_table();</pre>

    <div class="page-header">
        <h4>Judul Table</h4>
    </div>
    <p>membuat judul table</p>
    <pre class="prettyprint linenums">$table->set_title(clang::__("nama_label"));</pre>

    <div class="page-header">
        <h4>Fill Table Data</h4>
    </div>
    <p>untuk mengisi data table, dapat dilakukan dari 2 cara, yaitu dari query dan dari array</p>
    <p><strong>Membuat table dari array</strong></p>
    <pre class="prettyprint linenums">
$array=array(
	array(
		"code"=>"BA",
		"name"=>"Apel",
		"group"=>"Buah",
	),
	array(
		"code"=>"BJ",
		"name"=>"Jeruk",
		"group"=>"Buah",
	),
	array(
		"code"=>"KM",
		"name"=>"Mobil",
		"group"=>"Kendaraan",
	),
	array(
		"code"=>"BJ",
		"name"=>"Jambu",
		"group"=>"Buah",
	),
	
);
$table->set_data_from_array($array);

$table->add_column('code')->set_label('Kode');
$table->add_column('name')->set_label('Nama');
$table->add_column('group')->set_label('Group');
    </pre>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/from_array.png'); ?>"></img>
    <p><strong>Membuat table dari query(database)</strong></p>
    <pre class="prettyprint linenums">$table->set_data_from_query('select * from users');
		
$table->add_column('username')->set_label('Username');
$table->add_column('first_name')->set_label('Nama Depan');
$table->add_column('last_name')->set_label('Nama Belakang');
$table->add_column('created')->set_label('Created');</pre>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/from_query.png'); ?>"></img>

    <div class="page-header">
        Secara default table akan mengaplikasi kan plugin javascript datatable, jika ingin mematikan fungsi plugin :
    </div>
    <pre class="prettyprint linenums">$table->set_apply_data_table(false);</pre>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/from_query_datatable_false.png'); ?>"></img>
    <div class="page-header">
        <h4>Membuat Table Action</h4>
    </div>
    <p>Untuk membuat action, dapat dilakukan dengan fungsi add_row_action.</p>
    <p>Fungsi add_row_action akan mengembalikan object CAction</p>
    <pre class="prettyprint linenums">$action = $table->add_row_action()->set_icon('pencil');
$action = $table->add_row_action()->set_icon('trash');</pre>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/row_action_standard.png'); ?>"></img>
    <p>Secara default style row action akan menampilkan icon saja</p>
    <p>Style row action dapat dirubah dengan fungsi set_action_style</p>

    <pre class="prettyprint linenums">$table->set_action_style('btn-dropdown');
$action = $table->add_row_action()->set_icon('pencil')->set_label('Edit');
$action = $table->add_row_action()->set_icon('trash')->set_label('Delete');</pre>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/row_action_btn_dropdown.png'); ?>"></img>
    <div class="page-header">
        <h4>Action Style</h4>
    </div>
    <pre class="prettyprint linenums">$table->set_action_style("[btn-dropdown, btn-group, btn-list]");</pre>

    <div class="page-header">
        <h4>Set Confirm</h4>
    </div>
    <p>membuat konfimasi</p>
    <pre class="prettyprint linenums">$action = $table->add_row_action()->set_icon('trash')->set_label('Delete')->set_confirm(true);</pre>
    <p>isikan method dengan "TRUE" atau "FALSE" </p>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/confirm.png'); ?>"></img>

    <div class="page-header">
        <h4>Column</h4>
    </div>
    <p>membuat kolom table</p>
    <pre class="prettyprint linenums">$table->add_column('[nama_filed_database]');</pre>    
    <p>dengan menambahkan fungsi lain seperti :</p>
    <code>->set_label(clang::__("nama_label"))->set_align("[right, left, justify or center]")->add_transform("thousand_separator");</code>

    <div class="page-header">
        <h4>Ajax</h4>
    </div>
    <p>menampilkan isi table jika data pada database lebih dari 1000 record, sehingga ketika load data table tidak terlalu lama</p>
    <pre class="prettyprint linenums">$table->set_ajax(FALSE);</pre>
    <p>isikan method dengan "TRUE" atau "FALSE" </p>

    <div class="page-header">
        <h4>Set Combobox</h4>
    </div>
    <pre class="prettyprint linenums">$table->set_checkbox(true);
$table->set_checkbox_value($id);</pre>
    <p>isikan method dengan TRUE atau FALSE</p>
    <p>untuk mengisi value tiap combobox tambahkan code <span class="badge">set_checbox_value($id)</span></p>
    <p>Hasil</p>
    <img src="<?php echo c::manimgurl('table/checkbox.png'); ?>"></img>

    <div class="page-header">
        <h4>Cell CallBack Func</h4>
    </div>
    <p>jika ditemui kondisi dimana $data1 ditampilkan dan $data2 tidak, maka fungsi ini diperlukan</p>
    <pre class="prettyprint linenums">$table->cell_callback_func(array("nama_Controller", "cell_callback"));</pre>
    <p>untuk fungsi nya</p>
    <pre class="prettyprint linenums">public static function cell_callback($table, $col, $row, $text) {
    if($col=="is_active") {
            if($text) {
                    return clang::__("YES");				
            } else {
                    return clang::__("NO");			
            }
    }
    return $text;
}</pre>

    <div class="page-header">
        <h4>Set Data Dari Query</h4>
    </div>
    <pre class="prettyprint linenums">$table->set_data_from_query($q)->set_key($id);</pre>

    <div class="page-header">
        <h4>Export File</h4>
    </div>    
    <pre class="prettyprint linenums">$table->export_excel($filename, " ... ");</pre>
    <p>macam-macam export file:</p>
    <ul>
        <li>export_excel</li>
        <li>export_pdf</li>
        <li>export_excelcsv</li>
        <li>export_excelxml</li>
    </ul>

    <div class="page-header">
        <h4>Footer</h4>
    </div>
    <p>fungsi sebagai footer table dan informasi pada table tersebut. tambahkan fungsi <span class="badge">add_footer_field()</span></p>
    <pre class="prettyprint linenums">$table->set_footer(true);
$table->add_footer_field(clang::__(" ... "),ctransform::thousand_separator( $id ),"[right, left or center]");</pre>
    <p>isikan method dengan "TRUE" atau "FALSE" </p>
</div>