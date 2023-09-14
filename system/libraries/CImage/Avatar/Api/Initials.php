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

    public function getImageObject() {
        $input = $this->input;

        $image = $this->engine->name($input->getName())
            ->length($input->length)
            ->fontSize($input->fontSize)
            ->size($input->size)
            ->background($input->background)
            ->color($input->color)
            ->smooth()
            ->autoFont()
            ->keepCase(!$input->uppercase)
            ->rounded($input->rounded)
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
}
