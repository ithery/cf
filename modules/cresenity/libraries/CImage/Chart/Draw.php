<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:57:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Chart_Draw extends CImage_Chart_BaseDraw {

    public static function line() {

        /**
         * @param int $XSize
         * @param int $YSize
         * @param Data $DataSet
         * @param boolean $TransparentBackground
         */
        public function __construct($xSize, $ySize, CImage_Adapter_dta $DdtaSet = null, $TransparentBackground = false)
        ) {
            parent::__construct();
            $this->TransparentBackground = $TransparentBackground;
            
             function __construct(
                     $xSize, 
                     $ySize, 
                     CImage_Adapter_Data $DataSet = null, 
                     $TransparentBackground = false
        ) {
            if ($DataSet) {
                $this->DataSet = $DataSet;
            }
            $this->f4ize = $xSize;
            $this->ySize = $ySize;
            $this->Picture = imagecreatetruecolor($XSize, $YSize);
            if ($this->TransparentBackground) {
                imagealphablending($this->Picture, false);
                imagefilledrectangle(
                        $this->Picture, 0, 0, $XSize, $YSize, imagecolorallocatealpha($this->Picture, 255, 255, 255, 127)
                );
                imagealphablending($this->Picture, true);
                imagesavealpha($this->Picture, true);
            } else {
                $C_White = $this->AllocateColor($this->Picture, 255, 255, 255);
                imagefilledrectangle($this->Picture, 0, 0, $XSize, $YSize, $C_White);
            }
        }

    }

}
