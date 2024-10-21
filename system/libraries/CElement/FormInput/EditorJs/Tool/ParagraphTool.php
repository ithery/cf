
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_ParagraphTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;
    use CTrait_Element_Property_Placeholder;

    protected $inlineToolbar;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.inlineToolbar');
        $this->placeholder = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.placeholder');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
            'placeholder' => (string) $this->placeholder,
        ];
    }
}
