<div id="upload-file">    
    <div class="page-header">
        <h4>Upload Image</h4>
    </div> 
    <pre class="prettyprint linenums">
$form = $app->add_form('name')->set_enctype('multipart/form-data');
$widget = $form->add_widget();

$div_main = $widget->add_div()->add_class('row-fluid');
$div = $div_main;
$imgsrc = curl::base() . 'cresenity/noimage/120/120';
if (strlen($image) > 0) {
    $imgsrc = cimage::get_image_src("image", $id, "small", $image);
}

$span2->add_control('nama_image', 'image')->set_imgsrc($imgsrc)->set_maxwidth(120)->set_maxheight(120);</pre>
    <p>membuat fungsi upload file dengan format image</p>
    
    <div class="page-header">
        <h4>Upload File</h4>
    </div> 
    <pre class="prettyprint linenums">
$filename = "";
if (isset($_FILES["file"])) {
    $filename = $_FILES["file"]["name"];
}
$filename = cutils::sanitize($filename, true);

$random_prefix = cutils::randmd5() . "_";
$saved_filename = cutils::sanitize($random_prefix . $filename, true);
$upload_path = cupload::getpath("folder/", $saved_filename);
$fullfilename = cupload::save("file", $saved_filename, $upload_path);
$file_content = file_get_contents($fullfilename);
if (substr($file_content, 0, 2) == "PK") {
    $is_zip = 1;
    $zip_filename = $filename;
    $saved_zip_filename = $saved_filename;
    //it is zip file
    $zip = CZip::factory($fullfilename);
    $file_data = $zip->get_file_data();

    try {
        if (is_array($file_data)) {
            $filename = $file_data["filename"];
            $filename = cutils::sanitize($filename, true);
            $saved_filename = cutils::sanitize($random_prefix . $filename, true);
            $upload_path = cupload::getpath("folder/", $saved_filename);
            $fullfilename = $upload_path . $saved_filename;
            file_put_contents($fullfilename, $file_data["content"]);
        }
    } catch (Exception $ex) {
        $error++;
        $error_message = "Error, " . $ex->getMessage();
    }
}</pre>
    <p>membuat fungsi upload file</p>
</div>