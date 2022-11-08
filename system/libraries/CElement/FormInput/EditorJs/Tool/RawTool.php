
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_RawTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Placeholder;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.raw.enabled');
        $this->placeholder = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.raw.placeholder');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'placeholder' => (string) $this->placeholder,
        ];
    }
}
