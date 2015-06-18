<div id="user">
    <div class="page-header">
        <h4>Membuat Form Field</h4>
    </div>
    <pre class="prettyprint linenums">$field = $form->add_field()</pre>

    <div class="page-header">
        <h4>Memberi label untuk field</h4>
    </div>
    <pre class="prettyprint linenums">$field->set_label($label);</pre>
    <p>$method diisi dengan string label yang akan diisi</p>

    <div class="page-header">
        <h4>Menambah form control</h4>
    </div>
    <p></p>
    <pre class="prettyprint linenums">$field->add_control($id,$type);</pre>
    <p>$id diisi dengan string id untuk control tersebut</p>
    <p>$type diisi dengan string type control tersebut, type yang tersedia adalah:</p>
    <ul>
        <li>text</li>
        <li>password</li>
        <li>currency</li>
        <li>select</li>
        <li>textarea</li>
        <li>date</li>
        <li>time</li>
        <li>image</li>
        <li>ckeditor</li>
    </ul>
</div>