<div id="render">
    <div class="page-header">
        <h1>render</h1>
    </div>
    <p>fungsi render digunakan untuk menampilkan seluruh isi halaman mulai dari header, content, hingga footer </p>
    
    <h4>Fungsi render</h4>
    <pre class="prettyprint linenums">
public function render() {

    $theme_path = "";
    $theme_path = ctheme::path();

    if (ccfg::get("install")) {
        $v = CView::factory($theme_path . 'cinstall/page');
        
    } else if ($this->signup) {
        $v = CView::factory($theme_path . 'ccore/signup');
    } else if ($this->resend) {
        $v = CView::factory($theme_path . 'ccore/resend_activation');
    } else if ($this->activation) {
        $v = CView::factory($theme_path . 'ccore/activation');
    } else if (!$this->is_user_login() && ccfg::get("have_login")) {
        $v = CView::factory($theme_path . 'ccore/login');
    } else {
        $v = CView::factory($theme_path . 'cpage');

        $this->content = parent::html();
        $this->js = parent::js();
        $v->content = $this->content;
        $v->title = $this->title;
        $v->js = $this->js;
        $cs = CClientScript::instance();
        $v->head_client_script = $cs->render('head');
        $v->begin_client_script = $cs->render('begin');
        $v->end_client_script = $cs->render('end');

        $v->load_client_script = $cs->render('load');
        $v->ready_client_script = $cs->render('ready');
        $v->custom_js = $this->custom_js;
        $v->custom_header = $this->custom_header;
        $v->custom_footer = $this->custom_footer;
        $v->show_breadcrumb = $this->show_breadcrumb;
        $v->show_title = $this->show_title;
        $v->breadcrumb = $this->breadcrumb;
    }

    return $v->render();
}
</pre>
    <h4>Penggunaan</h4>

    <h2>Example</h2>
    <pre class="prettyprint linenums">
echo $app->render();</pre>
</div>