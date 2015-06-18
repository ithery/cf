<div id="db">
    <!-----------------database------------------->
    <div class="page-header">
        <h4>koneksi ke database</h4>
    </div>
    <p>gunakan fungsi tambahan dengan memanggil <span class="badge"> $app = CApp::instance();</span> sebelum atau sesudah fungsi database.</p>
    <pre class="prettyprint linenums">
$db = CDatabase::instance();</pre>

    <!-----------------show title------------------->
    <div class="page-header">
        <h4>Show Title</h4>
    </div>
    <p>mengaktifkan atau nonaktif penamaan atau judul</p>
    <pre class="prettyprint linenums">
 $app->show_title(true);</pre>    
    <p>isikan method dengan "TRUE" atau "FALSE"</p>

    <!-----------------title------------------->
    <div class="page-header">
        <h4>Judul Halaman</h4>
    </div>
    <pre class="prettyprint linenums">
$app->title(clang::__(" ... "));</pre>

    <!-----------------breadcrumb------------------->
    <div class="page-header">
        <h4>Show Breadcrumb</h4>
    </div>
    <pre class="prettyprint linenums">
$app->show_breadcrumb(true);</pre>    
    <p>isikan method dengan "TRUE" atau "FALSE" </p>

    <!-----------------org------------------->
    <div class="page-header">
        <h4>org</h4>
    </div>
    <p>mengambil data org (organization)</p>
    <pre class="prettyprint linenums">
$org = $app->org();</pre>

    <!-----------------store------------------->
    <div class="page-header">
        <h4>store</h4>
    </div>
    <p>membuat store namum sebelumnya harus memiliki org terlebih dahulu</p>
    <pre class="prettyprint linenums">$store = $app->store();</pre>

<!-----------------role------------------->   
    <div class="page-header">
        <h4>Role</h4>
    </div>
    <p>mengambil user role </p>
    <pre class="prettyprint linenums">
$role = $app->role();</pre>

<!-----------------Render------------------->   
    <div class="page-header">
        <h4>Render</h4>
    </div>
    <p>fungsi render digunakan untuk menampilkan seluruh isi halaman mulai dari header, content, hingga footer </p>
    <pre class="prettyprint linenums">
echo $app->render();</pre>
</div>