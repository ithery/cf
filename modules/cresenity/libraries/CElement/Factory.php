<?php

class CElement_Factory {

    /**
     * 
     * @param string $tag
     * @param string $id optional
     * @return \CElement_Element|boolean
     * @throws CApp_Exception
     */
    public static function createElement($tag = "div", $id = "") {
        $class_name = 'CElement_Element_';
        switch (strtolower($tag)) {
            case 'div':
            case 'span':
            case 'a':
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
            case 'ol':
            case 'ul':
            case 'li':
            case 'table':
            case 'th':
            case 'tr':
            case 'td':
            case 'tbody':
            case 'thead':
            case 'blockquote':
                $class_name = 'CElement_Element_' . ucfirst($tag);
                return new $class_name($id);
                break;
            default:
                throw new CApp_Exception('element [:tag] not found', array(':tag' => $tag));
                break;
        }
        return false;
    }

    /**
     * Backward compatibility of createElement
     * 
     * @param string $tag
     * @param string $id optional
     * @return \CElement_Element|boolean
     * @throws CApp_Exception
     */
    public static function create_element($tag = "div", $id = "") {
        return self::createElement($tag, $id);
    }

    /**
     * 
     * @param string $id optional
     * @return \CElement_Template|boolean
     */
    public static function createTemplate($id = "") {
        return new CElement_Template($id);
    }

    
    public static function createFormInput($class, $id="") {
        return new $class($id);
        
    }
}

?>