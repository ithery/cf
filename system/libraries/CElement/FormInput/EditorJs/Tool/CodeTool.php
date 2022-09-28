<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_CodeTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Placeholder;
    use CTrait_Element_Property_Shortcut;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.code.enabled');
        $this->shortcut = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.code.shortcut');
        $this->placeholder = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.code.placeholder');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'placeholder' => (string) $this->placeholder,
            'shortcut' => (string) $this->shortcut,
        ];
    }
}
