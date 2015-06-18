<div id="user">
    <div class="page-header">
        <h4>Membuat Input Text Currency</h4>
    </div>  
    <pre class="prettyprint linenums">$field->add_control($id,'currency')->set_align("right")->add_transform("thousand_separator")</pre>
	
	<div class="page-header">
        <h4>Memberi value untuk text</h4>
    </div>
	<pre class="prettyprint linenums">$field->set_value($value);</pre>
	<p>$value diisi dengan nilai control yang akan diisi</p>
</div>