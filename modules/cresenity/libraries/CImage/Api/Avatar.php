<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 2:13:06 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */


class CImage_Avatar {
    
    
    
    public static function generate() {
        $avatarEngine = CImage_Avatar_EngineFactory::($engineName);


        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 172800));

        
        
        $image = $avatar->name($input->name)
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
    }
}