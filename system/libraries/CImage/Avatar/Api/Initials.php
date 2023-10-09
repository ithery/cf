<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 2:25:13 AM
 */
class CImage_Avatar_Api_Initials {
    protected $engine;

    protected $input;

    public function __construct() {
        $this->engine = new CImage_Avatar_Engine_Initials();
        $this->input = new CImage_Avatar_Input_Initials();
    }

    public function setName($name) {
        $this->input->setName($name);

        return $this;
    }

    public function setSize($size) {
        $this->input->size = $size;

        return $this;
    }

    public function setBackground($background) {
        $this->input->background = $background;

        return $this;
    }

    public function setRounded($rounded = true) {
        $this->input->setRounded((bool) $rounded);

        return $this;
    }

    public function getImageObject() {
        $input = $this->input;

        $image = $this->engine->name($input->getName())
            ->length($input->length)
            ->fontSize($input->getFontSize())
            ->size($input->getSize())
            ->background($input->getBackground())
            ->color($input->getColor())
            ->smooth()
            ->autoFont()
            ->keepCase(!$input->getUppercase())
            ->rounded($input->getRounded())
            ->generate();

        return $image;
    }

    public function render() {
        return $this->getImageObject()->stream('png', 100);
    }

    /**
     * @return string
     */
    public function toBase64() {
        return (string) $this->getImageObject()->encode('data-url');
    }

    /**
     * @return string
     */
    public function toSvg() {
        $input = $this->input;
        $size = $inputWidth = $inputHeight = $input->getSize();
        $borderSize = $input->getBorderSize();
        $x = $y = $borderSize / 2;
        $width = $height = $inputWidth - $borderSize;
        $radius = ($inputWidth - $borderSize) / 2;
        $center = $inputWidth / 2;
        $borderColor = $input->getBorderColor();
        $borderRadius = $input->getBorderRadius();
        $background = $input->getBackground();

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $inputWidth . ' ' . $inputHeight . '">';
        $shape = $input->getRounded() ? 'circle' : 'square';
        if ($shape == 'square') {
            $svg .= '<rect x="' . $x
                . '" y="' . $y
                . '" width="' . $width . '" height="' . $height
                . '" stroke="' . $borderColor
                . '" stroke-width="' . $borderSize
                . '" rx="' . $borderRadius
                . '" fill="' . $background . '" />';
        } elseif ($shape == 'circle') {
            $svg .= '<circle cx="' . $center
                . '" cy="' . $center
                . '" r="' . $radius
                . '" stroke="' . $borderColor
                . '" stroke-width="' . $borderSize
                . '" fill="' . $background . '" />';
        }
        $fontSize = $input->getFontSize() * $size;

        $svg .= '<text font-size="' . $fontSize;
        $fontFamily = $input->getFontFamily();
        if ($fontFamily) {
            $svg .= '" font-family="' . $fontFamily;
        }
        $foreground = $input->getColor();
        $svg .= '" fill="' . $foreground . '" x="50%" y="50%" dy=".1em" style="line-height:1" alignment-baseline="middle" text-anchor="middle" dominant-baseline="central">';
        $svg .= $input->getInitials();
        $svg .= '</text>';

        $svg .= '</svg>';

        return $svg;
    }
}
