<?php

class CElement_Factory {
    /**
     * @param string $tag
     * @param string $id  optional
     *
     * @return \CElement_Element|bool
     *
     * @throws CApp_Exception
     */
    public static function createElement($tag = 'div', $id = '') {
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
            case 'p':
            case 'ol':
            case 'ul':
            case 'li':
            case 'table':
            case 'th':
            case 'tr':
            case 'td':
            case 'code':
            case 'pre':
            case 'tbody':
            case 'thead':
            case 'iframe':
            case 'blockquote':
            case 'label':
            case 'canvas':
            case 'img':
            case 'button':
                $class_name = 'CElement_Element_' . ucfirst($tag);
                return new $class_name($id);
                break;
            default:
                throw new CApp_Exception('element [:tag] not found', [':tag' => $tag]);
                break;
        }
        return false;
    }

    /**
     * @param string $id optional
     *
     * @return \CElement_Template|bool
     */
    public static function createTemplate($id = '') {
        return new CElement_Template($id);
    }

    /**
     * @param string                 $id   optional
     * @param CView_View|string|null $view optional
     * @param array|null             $data optional
     *
     * @return \CElement_View
     */
    public static function createView($id = '', $view = null, $data = null) {
        return new CElement_View($id, $view, $data);
    }

    /**
     * @param string $class
     * @param string $id
     *
     * @return \CElement_FormInput
     */
    public static function createFormInput($class, $id = '') {
        return new $class($id);
    }

    /**
     * @param string $name
     * @param string $id   optional
     *
     * @return \CElement_Component
     *
     * @throws CApp_Exception
     */
    public static function createComponent($name, $id = '') {
        $className = $name;
        if (!class_exists($className)) {
            $className = 'CElement_Component_' . $name;
        }
        if (!class_exists($className)) {
            throw new CApp_Exception('component [:name] not found', [':name' => $name]);
        }

        return new $className($id);
    }

    /**
     * @param string $name
     * @param string $id   optional
     *
     * @return \CElement_Composite
     *
     * @throws CApp_Exception
     */
    public static function createComposite($name, $id = '') {
        $className = 'CElement_Composite_' . $name;
        if (!class_exists($className)) {
            throw new CApp_Exception('composite element [:name] not found', [':name' => $name]);
        }

        return new $className($id);
    }

    /**
     * @param string $name
     * @param string $id   optional
     *
     * @return \CElement_List
     *
     * @throws CApp_Exception
     */
    public static function createList($name, $id = '') {
        $className = 'CElement_List_' . $name;
        if (!class_exists($className)) {
            throw new CApp_Exception('list element [:name] not found', [':name' => $name]);
        }

        return new $className($id);
    }

    public static function createViewComponent($componentName, $id) {
        return new CElement_ViewComponent($id, $componentName);
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @return CElement_FormInput
     *
     * @throws CException
     */
    public static function createControl($id, $type) {
        return CManager::instance()->createControl($id, $type);
    }

    public static function createPseudoElement($id = null) {
        return new CElement_PseudoElement();
    }
}