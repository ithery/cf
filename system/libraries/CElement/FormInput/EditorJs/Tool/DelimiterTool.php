
<?php
/**
 * Delimiter block tool configuration for EditorJS.
 *
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_DelimiterTool extends CElement_FormInput_EditorJs_ToolAbstract {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.delimiter.enabled');
    }

    /**
     * @return array
     */
    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
        ];
    }
}
