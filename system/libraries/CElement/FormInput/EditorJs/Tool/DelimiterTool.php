
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_DelimiterTool extends CElement_FormInput_EditorJs_ToolAbstract {
    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.delimiter.enabled');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
        ];
    }
}
