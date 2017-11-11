<?php

class CElement_Factory {

    public static function create_element($tag = "div", $id = "") {
        $class_name = 'CElement_Element_';
        switch (strtolower($tag)) {
            case 'div':
            case 'a':
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
            case 'ol':
            case 'li':
                $class_name = 'CElement_Element_' . ucfirst($tag);
                return new $class_name($id);
                break;
            default:
                throw new CApp_Exception('element :tag not found', array(':tag' => $tag));
                break;
        }
        return false;
    }

  
}

?>