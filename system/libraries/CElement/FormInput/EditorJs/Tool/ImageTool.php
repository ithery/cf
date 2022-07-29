
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_ImageTool extends CElement_FormInput_EditorJs_ToolAbstract {
    use CTrait_Element_Property_Shortcut;

    protected $services;

    protected $path;

    protected $disk;

    protected $alterations;

    protected $thumbnails;

    protected $isSimple;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.enabled');
        $this->shortcut = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.shortcut');
        $this->path = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.path');
        $this->disk = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.disk');
        $this->isSimple = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.isSimple');
        $this->alterations = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.alterations');
        $this->thumbnails = [];
        $this->addThumbnail('_small', CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.image.thumbnails._small'));
    }

    public function addThumbnail($key, array $options) {
        $this->thumbnails[$key] = $options;
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'isSimple' => (bool) $this->isSimple,
            'shortcut' => (string) $this->shortcut,
            'path' => (string) $this->path,
            'disk' => (string) $this->disk,
            'alterations' => (array) $this->alterations,
            'thumbnails' => (array) $this->thumbnails,

        ];
    }
}
