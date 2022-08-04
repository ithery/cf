
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_MarkerTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.marker.enabled');
        $this->shortcut = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.marker.shortcut');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'shortcut' => (string) $this->shortcut,
        ];
    }
}
