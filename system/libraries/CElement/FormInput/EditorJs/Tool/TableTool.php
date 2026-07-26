<?php
/**
 * Table block tool configuration for EditorJS.
 *
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_TableTool extends CElement_FormInput_EditorJs_ToolAbstract {
    /**
     * Whether the inline toolbar is shown for this tool.
     *
     * @var bool
     */
    protected $inlineToolbar;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.table.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.table.inlineToolbar');
    }

    /**
     * @return array
     */
    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
        ];
    }
}
