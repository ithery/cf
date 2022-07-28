
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_InlineCodeTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.inlineCode.enabled');
        $this->shortcut = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.inlineCode.shortcut');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'shortcut' => (string) $this->shortcut,
        ];
    }
}
