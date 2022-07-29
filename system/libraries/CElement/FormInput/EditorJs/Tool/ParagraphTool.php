
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_MarkerTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;

    protected $inlineToolbar;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.inlineToolbar');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'shortcut' => (string) $this->shortcut,
        ];
    }
}
