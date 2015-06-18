<div id="download-file">    
    <div class="page-header">
        <h4>Download File</h4>
    </div>
    <p>Export to xls/pdf/csv/xls xml</p> 
    <pre class="prettyprint linenums">
$request = array_merge($_GET, $_POST);
$download_type = "xls/pdf/csv/xls xml";
   if (isset($request["download_type"])) {
       $download_type = $request["download_type"];
   }

//button dropdown

$widget->add_control('submitted', 'hidden')->set_value("1");
$actions = $widget->add_action_list()->set_style('form-action');
$act_submit = $actions->add_action('submit_button')->set_label(clang::__("Submit"))->set_icon("ok")->set_submit(true)->set_link(curl::base() . 'url');
$act_d = $actions->add_action_list();
$act_download = $act_d->add_action('download_xls_button')->set_label(clang::__("Download XLS/PDF/CSV/XLS XML"))->set_icon("file")->set_submit_to(curl::base() . 'url?download=1&download_type=xls/csv/pdf/xls xml', '_blank');

//call function export    
if ($download == 1) {
$filename = date("YmdHis");
switch ($download_type) {
    case "xls/pdf/csv/xls xml":
        $filename = $rand . $filename . "-REPORT-CRESENITY_APP" . ".xls/pdf/csv/xls xml";
        $table->export_excel($filename, " ... ");

        break;
}</pre>
</div>