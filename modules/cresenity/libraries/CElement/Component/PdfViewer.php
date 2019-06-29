<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 29, 2019, 6:43:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_PdfViewer extends CElement_Component {

    use CTrait_Element_Property_Width,
        CTrait_Element_Property_Height;

    protected $pdfUrl;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "iframe";
        $this->width='100%';
        $this->height='500px';
    }

    public function build() {
        $url = curl::base() . 'cresenity/pdf?file=' . $this->pdfUrl;
        $this->setAttr('src', $url);

        $this->setAttr('width', $this->width);

        $this->setAttr('height', $this->height);
    }

    public function setPdfUrl($url) {
        $this->pdfUrl = $url;
        return $this;
    }

}
