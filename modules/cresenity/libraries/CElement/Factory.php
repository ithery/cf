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
            case 'iframe':
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
     * 
     * @param string $id optional
     * @return \CElement_Template|boolean
     */
    public static function createTemplate($id = "") {
        return new CElement_Template($id);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return \CElement_FormInput
     */
    public static function createFormInput($class, $id = "") {
        return new $class($id);
    }

    /**
     * 
     * @param string $name
     * @param string $id optional
     * @return \CElement_Component
     * @throws CApp_Exception
     */
    public static function createComponent($name, $id = "") {
        $className = 'CElement_Component_' . $name;
        if (!class_exists($className)) {
            throw new CApp_Exception('component [:name] not found', array(':name' => $name));
        }

        return new $className($id);
    }

    /**
     * 
     * @param string $name
     * @param string $id optional
     * @return \CElement_Composite
     * @throws CApp_Exception
     */
    public static function createComposite($name, $id = "") {
        $className = 'CElement_Composite_' . $name;
        if (!class_exists($className)) {
            throw new CApp_Exception('composite element [:name] not found', array(':name' => $name));
        }

        return new $className($id);
    }

    /**
     * 
     * @param string $name
     * @param string $id optional
     * @return \CElement_List
     * @throws CApp_Exception
     */
    public static function createList($name, $id = "") {
        $className = 'CElement_List_' . $name;
        if (!class_exists($className)) {
            throw new CApp_Exception('list element [:name] not found', array(':name' => $name));
        }

        return new $className($id);
    }

}

?>