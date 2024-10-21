<?php
/**
 * @see CManager_EditorJs
 */
class CElement_FormInput_EditorJs extends CElement_FormInput {
    use CTrait_Element_Property_Placeholder;
    use CElement_FormInput_EditorJs_Trait_EditorJsToolTrait;

    protected $editor;

    /**
     * @var string
     */
    protected $initialBlock;

    /**
     * @var bool
     */
    protected $autofocus;

    /**
     * @var string
     */
    protected $uploadImageByFileEndpoint;

    /**
     * @var string
     */
    protected $uploadImageByUrlEndpoint;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'hidden';
        $this->placeholder = CElement_FormInput_EditorJs_DefaultConfig::get('editorSettings.placeholder');
        $this->initialBlock = CElement_FormInput_EditorJs_DefaultConfig::get('editorSettings.initialBlock');
        $this->autofocus = CElement_FormInput_EditorJs_DefaultConfig::get('editorSettings.autofocus');
        $this->tools = [
            'header' => new CElement_FormInput_EditorJs_Tool_HeaderTool(),
            'checklist' => new CElement_FormInput_EditorJs_Tool_ChecklistTool(),
            'code' => new CElement_FormInput_EditorJs_Tool_CodeTool(),
            'delimiter' => new CElement_FormInput_EditorJs_Tool_DelimiterTool(),
            'embed' => new CElement_FormInput_EditorJs_Tool_EmbedTool(),
            'image' => new CElement_FormInput_EditorJs_Tool_ImageTool(),
            'inlineCode' => new CElement_FormInput_EditorJs_Tool_InlineCodeTool(),
            'link' => new CElement_FormInput_EditorJs_Tool_LinkTool(),
            'list' => new CElement_FormInput_EditorJs_Tool_ListTool(),
            'marker' => new CElement_FormInput_EditorJs_Tool_MarkerTool(),
            'paragraph' => new CElement_FormInput_EditorJs_Tool_ParagraphTool(),
            'raw' => new CElement_FormInput_EditorJs_Tool_RawTool(),
            'table' => new CElement_FormInput_EditorJs_Tool_TableTool(),
        ];
        $this->uploadImageByFileEndpoint = c::url('cresenity/editorjs/upload/file');
        $this->uploadImageByUrlEndpoint = c::url('cresenity/editorjs/upload/url');
    }

    public function setUploadImageByFileEndpoint($url) {
        $this->uploadImageByFileEndpoint = $url;

        return $this;
    }

    public function setUploadImageByUrlEndpoint($url) {
        $this->uploadImageByUrlEndpoint = $url;

        return $this;
    }

    public function setInitialBlock($block) {
        $this->initialBlock = $block;

        return $this;
    }

    public function holderId() {
        return $this->id . '-editor';
    }

    public function build() {
        $divHolder = $this->before()->addDiv($this->holderId())->addClass('editorjs-holder cres-editor-js');
        $divHolder->setAttr('data-input-id', $this->id);
        $this->addClass('cres:element:control:EditorJs');
        $this->setAttr('cres-element', 'control:EditorJs');
        $this->setAttr('cres-config', c::json($this->buildControlConfig()));

        $this->setAttr('data-holder-id', $this->holderId());
        $this->setAttr('type', $this->type);
        $value = $this->value;
        if (is_string($value)) {
            if (strlen($value) > 0) {
                $origValue = $value;
                $value = json_decode($value, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    $value = [];
                }
            } else {
                $value = [];
            }
        }
        $this->setAttr('value', c::json($value));
        $manager = c::manager();
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/list@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/quote@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/image@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/code@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/table@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/link@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/warning@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/marker@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/inline-code@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/embed@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/raw@latest');
        // $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/table@latest');

        $manager->registerJs('plugins/editorjs/editorjs-2.28.0.js');
        $manager->registerJs('plugins/editorjs/simple-image-1.5.1.js');
        $manager->registerJs('plugins/editorjs/list-1.8.0.js');
        $manager->registerJs('plugins/editorjs/quote-2.5.0.js');
        $manager->registerJs('plugins/editorjs/image-2.8.1.js');
        $manager->registerJs('plugins/editorjs/code-2.8.0.js');
        $manager->registerJs('plugins/editorjs/table-2.2.2.js');
        $manager->registerJs('plugins/editorjs/link-2.5.0.js');
        $manager->registerJs('plugins/editorjs/warning-1.3.0.js');
        $manager->registerJs('plugins/editorjs/marker-1.3.0.js');
        $manager->registerJs('plugins/editorjs/inline-code-1.4.0.js');
        $manager->registerJs('plugins/editorjs/checklist-1.5.0.js');
        $manager->registerJs('plugins/editorjs/delimiter-1.3.0.js');
        $manager->registerJs('plugins/editorjs/embed-2.5.3.js');
        $manager->registerJs('plugins/editorjs/raw-2.4.0.js');
    }

    protected function buildControlConfig() {
        $editorSettings = [
            'placeholder' => (string) $this->placeholder,
            'initialBlock' => $this->initialBlock,
            'autofocus' => (bool) $this->autofocus,
        ];

        $toolSettings = c::collect($this->tools)->map(function ($tool) {
            return $tool->getConfig();
        })->toArray();

        $config = [
            'editorSettings' => $editorSettings,
            'toolSettings' => $toolSettings,
            'uploadImageByFileEndpoint' => $this->uploadImageByFileEndpoint,
            'uploadImageByUrlEndpoint' => $this->uploadImageByUrlEndpoint,

        ];

        return $config;
    }
}
