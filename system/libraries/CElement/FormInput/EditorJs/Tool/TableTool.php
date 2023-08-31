<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_TableTool extends CElement_FormInput_EditorJs_ToolAbstract {
    protected $inlineToolbar;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.table.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.table.inlineToolbar');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
        ];
    }
}
