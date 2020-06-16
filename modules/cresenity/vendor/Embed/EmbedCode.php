<?php

//declare(strict_types = 1);

namespace Embed;

use JsonSerializable;

class EmbedCode implements JsonSerializable {

    public $html;
    public $width;
    public $height;
    public $ratio;

    public function __construct($html, $width = null, $height = null) {
        $this->html = $html;
        $this->width = $width;
        $this->height = $height;

        if ($width && $height) {
            $this->ratio = round(($height / $width) * 100, 3);
        }
    }

    public function __toString() {
        return $this->html;
    }

    public function jsonSerialize() {
        return [
            'html' => $this->html,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

}
