
<?php
/**
 * Embed block tool configuration for EditorJS.
 *
 * @see CElement_FormInput_EditorJs
 */
class CElement_FormInput_EditorJs_Tool_EmbedTool extends CElement_FormInput_EditorJs_ToolAbstract {
    /**
     * Whether the inline toolbar is shown for this tool.
     *
     * @var bool
     */
    protected $inlineToolbar;

    /**
     * Enabled state of each embeddable service, keyed by service name.
     *
     * @var array
     */
    protected $services;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->enabled = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.embed.enabled');
        $this->inlineToolbar = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.embed.inlineToolbar');
        $this->services = CElement_FormInput_EditorJs_DefaultConfig::get('toolSettings.embed.services');
    }

    /**
     * @return array
     */
    public function getConfig() {
        return [
            'enabled' => (bool) $this->enabled,
            'inlineToolbar' => $this->inlineToolbar,
            'services' => c::collect($this->services)->map(function ($val) {
                return (bool) $val;
            })->toArray()
        ];
    }

    /**
     * @return $this
     */
    public function enable() {
        $this->enabled = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disable() {
        $this->enabled = false;
        return $this;
    }

    /**
     * @param string $service
     *
     * @return $this
     */
    protected function enableService($service) {
        $this->services[$service] = true;

        return $this;
    }

    /**
     * @param string $service
     *
     * @return $this
     */
    protected function disableService($service) {
        $this->services[$service] = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableYoutube() {
        return $this->enableService('youtube');
    }

    /**
     * @return $this
     */
    public function disableYoutube() {
        return $this->disableService('youtube');
    }

    /**
     * @return $this
     */
    public function enableCodepen() {
        return $this->enableService('codepen');
    }

    /**
     * @return $this
     */
    public function disableCodepen() {
        return $this->disableService('codepen');
    }

    /**
     * @return $this
     */
    public function enableVimeo() {
        return $this->enableService('vimeo');
    }

    /**
     * @return $this
     */
    public function disableVimeo() {
        return $this->disableService('vimeo');
    }

    /**
     * @return $this
     */
    public function enableImgur() {
        return $this->enableService('imgur');
    }

    /**
     * @return $this
     */
    public function disableImgur() {
        return $this->disableService('imgur');
    }
}
