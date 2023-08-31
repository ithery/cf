
<?php
/**
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_EmbedTool extends CElement_FormInput_EditorJs_ToolAbstract {
    protected $inlineToolbar;

    protected $services;

    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.embed.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.embed.inlineToolbar');
        $this->services = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.embed.services');
    }

    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
            'services' => c::collect($this->services)->map(function ($val) {
                return (bool) $val;
            })->toArray()
        ];
    }

    protected function enableService($service) {
        $this->services[$service] = true;

        return $this;
    }

    protected function disableService($service) {
        $this->services[$service] = false;

        return $this;
    }

    public function enableYoutube() {
        return $this->enableService('youtube');
    }

    public function disableYoutube() {
        return $this->disableService('youtube');
    }

    public function enableCodepen() {
        return $this->enableService('codepen');
    }

    public function disableCodepen() {
        return $this->disableService('codepen');
    }

    public function enableVimeo() {
        return $this->enableService('vimeo');
    }

    public function disableVimeo() {
        return $this->disableService('vimeo');
    }

    public function enableImgur() {
        return $this->enableService('imgur');
    }

    public function disableImgur() {
        return $this->disableService('imgur');
    }
}
