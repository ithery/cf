
<?php
/**
 * Paragraph block tool configuration for EditorJS.
 *
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_ParagraphTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;
    use CTrait_Element_Property_Placeholder;

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
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.inlineToolbar');
        $this->placeholder = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.paragraph.placeholder');
    }

    /**
     * @return array
     */
    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
            'placeholder' => (string) $this->placeholder,
        ];
    }
}
