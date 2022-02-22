<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 6:52:38 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_FormInput_Image {

    public function set_imgsrc($imgsrc) {
        return $this->setImgSrc($imgsrc);
    }

    public function set_maxwidth($maxwidth) {
        return $this->setMaxWidth($maxwidth);
    }

    public function set_maxheight($maxheight) {
        return $this->setMaxHeight($maxheight);
    }

    public function set_disabled_upload($bool) {
        return $this->setDisabledUpload($bool);
    }

}
