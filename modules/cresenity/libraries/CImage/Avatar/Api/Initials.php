<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 2:25:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Avatar_Api_Initials {

    public static function render() {
        $avatarEngine = new CImage_Avatar_Engine_Initials();


        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 172800));

        $input = new CImage_Avatar_Input_Initials();

        $image = $avatarEngine->name($input->name)
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

        echo $image->stream('png', 100);
    }

}
