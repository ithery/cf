<?php

/**
 * Class ConfigLoader.
 */
class CElement_FormInput_EditorJs_ConfigLoader {
    public $tools = [];

    /**
     * ConfigLoader constructor.
     *
     * @param string $configuration – configuration data
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     */
    public function __construct($configuration) {
        if (empty($configuration)) {
            throw new CElement_FormInput_EditorJs_EditorJsException('Configuration data is empty');
        }

        $config = json_decode($configuration, true);
        $this->loadTools($config);
    }

    /**
     * Load settings for tools from configuration.
     *
     * @param array $config
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     */
    private function loadTools($config) {
        if (!isset($config['tools'])) {
            throw new CElement_FormInput_EditorJs_EditorJsException('Tools not found in configuration');
        }

        foreach ($config['tools'] as $toolName => $toolData) {
            if (isset($this->tools[$toolName])) {
                throw new CElement_FormInput_EditorJs_EditorJsException("Duplicate tool ${toolName} in configuration");
            }

            $this->tools[$toolName] = $this->loadTool($toolData);
        }
    }

    /**
     * Load settings for tool.
     *
     * @param array $data
     *
     * @return array
     */
    private function loadTool($data) {
        return $data;
    }
}
