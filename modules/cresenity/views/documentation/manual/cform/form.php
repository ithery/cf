<div id="user">
    <div class="page-header">
        <h4>Membuat Form</h4>
    </div> 
    <pre class="prettyprint linenums">$form = CForm::factory();</pre>

    <div class="page-header">
        <h4>Set Form Method</h4>
    </div>
    <p>Mengatur action form saat submit</p>
    <pre class="prettyprint linenums">$form->set_method($method);</pre>
    <p>$method diisi dengan 'get' atau 'post'</p>
    
    <div class="page-header">
        <h4>Set Form Action</h4>
    </div>
    <pre class="prettyprint linenums">$form->set_action($url);</pre>
    <p>$url diisi dengan url dimana data form akan dikirim</p>
    
    <div class="page-header">
        <h4>Set Enctype</h4>
    </div>
    <pre class="prettyprint linenums">$form = $app->add_form('nama')->set_enctype('multipart/form-data');</pre>
    <p>enctype digunakan dalam proses Upload file supaya file dapat dikenali.</p>
</div>