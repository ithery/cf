<?php
class cinit{
	public static function check_for_extension($arrExt = array()) {
        if (PHP_OS == "Minix") 
           $arrReq = array('simplexml', 'pcre', 'xml', 'dom');
        if (PHP_OS == "WINNT") 
            $arrReq = array('simplexml', 'pcre', 'xml', 'mbstring', 'dom', 'com_dotnet');
        else 
            $arrReq = array('simplexml', 'pcre', 'xml', 'mbstring', 'dom');
        $extensions = array_merge($arrExt, $arrReq);
        $text = "";
        $error = false;
        $text .= "<?xml version='1.0'?>\n";
        $text .= "<phpsysinfo>\n";
        $text .= "  <Error>\n";
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $text .= "    <Function>checkForExtensions</Function>\n";
                $text .= "    <Message>phpSysInfo requires the ".$extension." extension to php in order to work properly.</Message>\n";
                $error = true;
            }
        }
        $text .= "  </Error>\n";
        $text .= "</phpsysinfo>";
        if ($error) {
            header("Content-Type: text/xml\n\n");
            echo $text;
            die();
        }
    }
}

?>