<div id="renderable">
    <!-----------------database------------------->
    <div class="page-header">
        <h4>Daftar Renderable</h4>
    </div>
    <pre class="prettyprint linenums">$app->add($string|$object);
$app->add_div();
$app->add_widget();
$app->add_table();
$app->add_form();
$app->add_action_list();
$app->add_action();</pre> 
    <p>Fungsi dapat mempunyai parameter string atau object, fungsi akan langsung menambahkan string/object sesuai urutan yang ada</p>
    <p>Setiap fungsi add_xxx akan menghasilkan return value sesuai object yang dibuat, sebagai contoh fungsi add_div akan menghasilkan object CDiv, fungsi add_table akan menghasilkan return value CTable</p>

    <pre class="prettyprint linenums">$app->add('start luar div');
$div = $app->add_div();
$div->add('dalam div');
$app->add('end luar div');</pre> 	

    <p>akan menghasilkan html seperti dibawah:</p>
    <pre class="prettyprint linenums">start luar div	&lt;div id="004a1416820cc8c2972a124681375ad7" class=""&gt;dalam div&lt;/div&gt;end luar div</pre>
    
    <p>Untuk attribute ID, CApp akan otomatis menggeneratekan id secara acak. jika tidak menginginkan ID autogenerate maka id dapat dipassing lewat parameter</p>
    <pre class="prettyprint linenums">$app->add('start luar div');
$div = $app->add_div('ini_id');
$div->add('dalam div');
$app->add('end luar div');</pre> 	
    <pre class="prettyprint linenums">start luar div	&lt;div id="ini_id" class=""&gt;dalam div&lt;/div&gt;end luar div
    </pre>
    <p>Setiap object capp dapat ditambahkan custom class secara manual melalui fungsi add_class</p>
    <pre class="prettyprint linenums">$app->add('start luar div');
$app->add_div('div1')->add_class('class1')->add('Dalam DIV 1');
$app->add_div('div2')->add_class('class2')->add('Dalam DIV 2');
$app->add_div('div3')->add_class('class3a class3b')->add('Dalam DIV 2');
$app->add('end luar div');
    </pre> 	
    <pre class="prettyprint linenums">$app->add('start luar div');
start luar div	&lt;div id=&quot;div1&quot; class=&quot; class1&quot;&gt; Dalam DIV 1 &lt;/div&gt;	
&lt;div id=&quot;div2&quot; class=&quot; class2&quot;&gt; Dalam DIV 2 &lt;/div&gt;	
&lt;div id=&quot;div3&quot; class=&quot; class3a class3b&quot;&gt; Dalam DIV 3 &lt;/div&gt; end luar div</pre> 		

</div>