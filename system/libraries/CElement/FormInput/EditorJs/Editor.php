<?php

class CElement_FormInput_EditorJs_Editor {
    /**
     * @var string
     */
    private $holder = '';

    /**
     * @var null|EditorConfig
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

    public function getHolder(): string {
        return $this->holder;
    }

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
