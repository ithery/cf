<?php

class CDocument_Pdf_Object_GrayColor extends CColor_Format_Rgb {
    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate($code) {
        if ($code > 255) {
            return false;
        }

        return $code;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color) {
        return list($this->red, $this->green, $this->blue) = [$color, $color, $color];
    }

    public function getGray() {
        return (float) $this->red;
    }
}
