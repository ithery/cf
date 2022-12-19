<?php
/**
 * @see CImage
 */
class CImage_Graph {
    public function __construct($width, $height, $cacheName = null, $inline = true) {
        if (!is_numeric($width) || !is_numeric($height)) {
            throw CImage_Graph_Exception::invalidArgument('Image width/height argument must be numeric');
        }

        // Initialize frame and margin
        $this->initializeFrameAndMargin();
        if ($cacheName == null) {
            $cacheName = $this->generateImgName();
        }
        // Should the image be streamed back to the browser or only to the cache?
        $this->inline = $inline;

        //$this->img = new RotImage($width,$height);
    }

    public function initializeFrameAndMargin() {
    }

    public function generateImgName() {
        // Determine what format we should use when we save the images
        $supported = imagetypes();
        if ($supported & IMG_PNG) {
            $img_format = 'png';
        } elseif ($supported & IMG_GIF) {
            $img_format = 'gif';
        } elseif ($supported & IMG_JPG) {
            $img_format = 'jpeg';
        } elseif ($supported & IMG_WBMP) {
            $img_format = 'wbmp';
        } elseif ($supported & IMG_XPM) {
            $img_format = 'xpm';
        }

        if (!isset($_SERVER['PHP_SELF'])) {
            throw new CImage_Graph_Exception(" Can't access PHP_SELF, PHP global variable. You can't run PHP from command line if you want to use the 'auto' naming of cache or image files.");
        }
        $fname = basename($_SERVER['PHP_SELF']);
        if (!empty($_SERVER['QUERY_STRING'])) {
            $q = @$_SERVER['QUERY_STRING'];
            $fname .= '_' . preg_replace("/\W/", '_', $q) . '.' . $img_format;
        } else {
            $fname = substr($fname, 0, strlen($fname) - 4) . '.' . $img_format;
        }

        return $fname;
    }
}
