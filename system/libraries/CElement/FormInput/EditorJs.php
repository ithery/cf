<?php

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
            'raw' => new CElement_FormInput_EditorJs_Tool_RawTool(),
            'table' => new CElement_FormInput_EditorJs_Tool_TableTool(),
        ];
        $this->uploadImageByFileEndpoint = c::url('cresenity/editorjs/upload/file');
        $this->uploadImageByUrlEndpoint = c::url('cresenity/editorjs/upload/url');
    }

    public function holderId() {
        return $this->id . '-editor';
    }

    public function build() {
        $divHolder = $this->before()->addDiv($this->holderId())->addClass('editorjs-holder cres-editor-js');
        $divHolder->setAttr('data-input-id', $this->id);
        $this->addClass('cres:element:control:EditorJs');
        $this->setAttr('cres-element', 'control:EditorJs');
        $this->setAttr('cres-config', c::jsonAttr($this->buildControlConfig()));

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
        $this->setAttr('value', c::jsonAttr($value));
        $manager = c::manager();
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/header@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/list@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/quote@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/image@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/code@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/table@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/link@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/warning@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/marker@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/inline-code@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/embed@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/paragraph@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/raw@latest');
        $manager->registerJs('https://cdn.jsdelivr.net/npm/@editorjs/table@latest');
    }

    protected function buildControlConfig() {
        $editorSettings = [
            'placeholder' => (string) $this->placeholder,
            'initialBlock' => (string) $this->initialBlock,
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
