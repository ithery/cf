<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_ChecklistTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;

    protected $inlineToolbar;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.checklist.enabled');
        $this->shortcut = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.checklist.shortcut');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.checklist.inlineToolbar');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
            'shortcut' => (string) $this->shortcut,
        ];
    }
}
