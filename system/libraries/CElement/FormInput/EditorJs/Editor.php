<?php

/**
 * EditorJS editor instance value object (holder, config and block data).
 */
class CElement_FormInput_EditorJs_Editor {
    /**
     * @var string
     */
    private $holder = '';

    /**
     * @var null|CElement_FormInput_EditorJs_EditorConfig
     */
    private $config;

    /**
     * @var CElement_FormInput_EditorJs_ToolConfig[]
     */
    private $tools = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor.
     *
     * @param string                                       $holder
     * @param null|CElement_FormInput_EditorJs_EditorConfig $config
     * @param array                                        $data
     */
    public function __construct(
        string $holder = '',
        ?CElement_FormInput_EditorJs_EditorConfig $config = null,
        array $data = []
    ) {
        $this->holder = $holder;

        if (!$config) {
            $config = new CElement_FormInput_EditorJs_EditorConfig('', new CElement_FormInput_EditorJs_ToolConfigCollection());
        }
        $this->config = $config;
        $this->tools = $config->getTools();

        $this->data = $data;
    }

    /**
     * Get the holder element ID the editor attaches to.
     *
     * @return string
     */
    public function getHolder(): string {
        return $this->holder;
    }

    /**
     * Set the holder element ID the editor attaches to.
     *
     * @param string $holder
     *
     * @return CElement_FormInput_EditorJs_Editor
     */
    public function setHolder(string $holder): CElement_FormInput_EditorJs_Editor {
        $this->holder = $holder;

        return $this;
    }

    /**
     * @return null|CElement_FormInput_EditorJs_EditorConfig
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @param CElement_FormInput_EditorJs_EditorConfig $config
     *
     * @return CElement_FormInput_EditorJs_Editor
     */
    public function setConfig(CElement_FormInput_EditorJs_EditorConfig $config) {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the raw block data.
     *
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return CElement_FormInput_EditorJs_Editor
     */
    public function setData(array $data) {
        $this->data = $data;

        return $this;
    }
}
