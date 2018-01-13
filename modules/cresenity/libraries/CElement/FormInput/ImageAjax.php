<?php

/**
 * Description of ImageAjax
 *
 * @author Hery
 */
class CElement_FormInput_ImageAjax extends CElement_FormInput {

    protected $imgsrc;
    protected $maxwidth;
    protected $maxheight;
    protected $disabled_upload;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "text";
        $this->imgsrc = "";
        $this->maxwidth = "200";
        $this->maxheight = "150";
        $this->disabled_upload = false;
    }

    public function set_imgsrc($imgsrc) {
        $this->imgsrc = $imgsrc;
        return $this;
    }

    public function set_maxwidth($maxwidth) {
        $this->maxwidth = $maxwidth;
        return $this;
    }

    public function set_maxheight($maxheight) {
        $this->maxheight = $maxheight;
        return $this;
    }

    public function set_disabled_upload($bool) {
        $this->disabled_upload = $bool;
        return $this;
    }

    protected function containerId() {
        return 'div-image-ajax-' . $this->id;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $attr = '';
        foreach ($this->attr as $k => $v) {
            $attr.=$k . '="' . $v . '" ';
        }
        $html->appendln('<div class="fileupload fileupload-new" >');
        $html->appendln('	<div class="fileupload-new thumbnail" ><img ' . $attr . ' id="cimg_' . $this->id . '" src="' . $this->imgsrc . '" style="max-width: ' . $this->maxwidth . 'px; max-height: ' . $this->maxheight . 'px;"  /></div>');
        $html->appendln('	<div class="fileupload-preview fileupload-exists thumbnail" style="max-width: ' . $this->maxwidth . 'px; max-height: ' . $this->maxheight . 'px; line-height: 20px;"></div>');
        $html->appendln('	<div>');
        if ($this->disabled_upload == false) {
            if ($this->bootstrap == '3.3') {
                $html->appendln('		<span class="btn btn-file btn-primary">');
            } else {
                $html->appendln('		<span class="btn btn-file">');
            }
            $html->appendln('			<span class="fileupload-new">' . clang::__('Select Image') . '</span>');
            $html->appendln('			<span class="fileupload-exists">' . clang::__('Change') . '</span>');
            $html->appendln('				<input type="file" name="' . $this->name . '" id="' . $this->id . '"/>');
            $html->appendln('		</span>');
            if ($this->bootstrap == '3.3') {
                $html->appendln('		<a href="#" class="btn fileupload-exists btn-danger" data-dismiss="fileupload">' . clang::__('Remove') . '</a>');
            } else {
                $html->appendln('		<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">' . clang::__('Remove') . '</a>');
            }
        }
        $html->appendln('	</div>');
        $html->appendln('</div>');

        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $ajax_url = CAjaxMethod::factory()->set_type('imageajax')
                ->set_data('input_name', $this->name)
                ->makeurl();

        $js->set_indent($indent);
        return $js->text();
    }

}
