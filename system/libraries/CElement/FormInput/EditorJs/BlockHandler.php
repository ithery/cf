<?php

/**
 * Class BlockHandler.
 */
class CElement_FormInput_EditorJs_BlockHandler {
    /**
     * Default pseudo-key for numerical arrays.
     */
    const DEFAULT_ARRAY_KEY = '-';

    /**
     * @var CElement_FormInput_EditorJs_EditorConfig
     */
    private $rules = null;

    /**
     * BlockHandler constructor.
     *
     * @param string $configuration
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     */
    public function __construct($configuration = null) {
        require_once DOCROOT . 'system/vendor/HTMLPurifier.auto.php';
        $this->rules = new CElement_FormInput_EditorJs_ConfigLoader($configuration);
    }

    /**
     * Validate block for correctness.
     *
     * @param string $blockType
     * @param array  $blockData
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     *
     * @return bool
     */
    public function validateBlock($blockType, $blockData) {
        /**
         * Default action for blocks that are not mentioned in a configuration.
         */
        if (!array_key_exists($blockType, $this->rules->tools)) {
            throw new CElement_FormInput_EditorJs_EditorJsException("Tool `${blockType}` not found in the configuration");
        }

        $rule = $this->rules->tools[$blockType];

        return $this->validate($rule, $blockData);
    }

    /**
     * Apply sanitizing rules according to the block type.
     *
     * @param string $blockType
     * @param array  $blockData
     * @param array  $blockTunes
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     *
     * @return array|bool
     */
    public function sanitizeBlock($blockType, $blockData, $blockTunes = []) {
        $rule = $this->rules->tools[$blockType];

        return [
            'type' => $blockType,
            'data' => $this->sanitize($rule, $blockData),
            'tunes' => $blockTunes
        ];
    }

    /**
     * Apply validation rule to the data block.
     *
     * @param array $rules
     * @param array $blockData
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     *
     * @return bool
     */
    private function validate($rules, $blockData) {
        /**
         * Make sure that every required param exists in data block.
         */
        foreach ($rules as $key => $value) {
            if (($key != CElement_FormInput_EditorJs_BlockHandler::DEFAULT_ARRAY_KEY) && (isset($value['required']) ? $value['required'] : true)) {
                if (!isset($blockData[$key])) {
                    throw new CElement_FormInput_EditorJs_EditorJsException("Not found required param `${key}`");
                }
            }
        }

        /**
         * Check if there is not extra params (not mentioned in configuration rule).
         */
        foreach ($blockData as $key => $value) {
            if (!is_integer($key) && !isset($rules[$key])) {
                throw new CElement_FormInput_EditorJs_EditorJsException("Found extra param `${key}`");
            }
        }

        /**
         * Validate every key in data block.
         */
        foreach ($blockData as $key => $value) {
            /**
             * PHP Array has integer keys.
             */
            if (is_integer($key)) {
                $key = CElement_FormInput_EditorJs_BlockHandler::DEFAULT_ARRAY_KEY;
            }

            $rule = $rules[$key];

            $rule = $this->expandToolSettings($rule);

            $elementType = $rule['type'];

            /**
             * Process canBeOnly rule.
             */
            if (isset($rule['canBeOnly'])) {
                if (!in_array($value, $rule['canBeOnly'])) {
                    throw new CElement_FormInput_EditorJs_EditorJsException("Option '${key}' with value `${value}` has invalid value. Check canBeOnly param.");
                }

                // Do not perform additional elements validation in any case
                continue;
            }

            /**
             * Do not check element type if it is not required and null.
             */
            if (isset($rule['required']) && $rule['required'] === false
                && isset($rule['allow_null']) && $rule['allow_null'] === true && $value === null
            ) {
                continue;
            }

            /**
             * Validate element types.
             */
            switch ($elementType) {
                case 'string':
                    if (!is_string($value)) {
                        throw new CElement_FormInput_EditorJs_EditorJsException("Option '${key}' with value `${value}` must be string");
                    }

                    break;

                case 'integer':
                case 'int':
                    if (!is_integer($value)) {
                        throw new CElement_FormInput_EditorJs_EditorJsException("Option '${key}' with value `${value}` must be integer");
                    }

                    break;

                case 'array':
                    $this->validate($rule['data'], $value);

                    break;

                case 'boolean':
                case 'bool':
                    if (!is_bool($value)) {
                        throw new CElement_FormInput_EditorJs_EditorJsException("Option '${key}' with value `${value}` must be boolean");
                    }

                    break;

                default:
                    throw new CElement_FormInput_EditorJs_EditorJsException("Unhandled type `${elementType}`");
            }
        }

        return true;
    }

    /**
     * Sanitize strings in the data block.
     *
     * @param array $rules
     * @param array $blockData
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     *
     * @return array
     */
    private function sanitize($rules, $blockData) {
        /**
         * Sanitize every key in data block.
         */
        foreach ($blockData as $key => $value) {
            /**
             * PHP Array has integer keys.
             */
            if (is_integer($key)) {
                $rule = $rules[CElement_FormInput_EditorJs_BlockHandler::DEFAULT_ARRAY_KEY];
            } else {
                $rule = $rules[$key];
            }

            $rule = $this->expandToolSettings($rule);
            $elementType = $rule['type'];

            /**
             * Sanitize string with Purifier.
             */
            if ($elementType == 'string') {
                $allowedTags = isset($rule['allowedTags']) ? $rule['allowedTags'] : '';
                if ($allowedTags !== '*') {
                    $blockData[$key] = $this->getPurifier($allowedTags)->purify($value);
                }
            }

            /**
             * Sanitize nested elements.
             */
            if ($elementType == 'array') {
                $blockData[$key] = $this->sanitize($rule['data'], $value);
            }
        }

        return $blockData;
    }

    /**
     * Create and return new default purifier.
     *
     * @param $allowedTags
     *
     * @return \HTMLPurifier
     */
    private function getPurifier($allowedTags) {
        $sanitizer = $this->getDefaultPurifier();

        $sanitizer->set('HTML.Allowed', $allowedTags);

        /**
         * Define custom HTML Definition for mark tool.
         */
        if ($def = $sanitizer->maybeGetRawHTMLDefinition()) {
            $def->addElement('mark', 'Inline', 'Inline', 'Common');
        }

        $purifier = new \HTMLPurifier($sanitizer);

        return $purifier;
    }

    /**
     * Initialize HTML Purifier with default settings.
     */
    private function getDefaultPurifier() {
        $sanitizer = \HTMLPurifier_Config::createDefault();

        $sanitizer->set('HTML.TargetBlank', true);
        $sanitizer->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
        $sanitizer->set('AutoFormat.RemoveEmpty', true);
        $sanitizer->set('HTML.DefinitionID', 'html5-definitions');

        $cacheDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'purifier';
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        $sanitizer->set('Cache.SerializerPath', $cacheDirectory);

        return $sanitizer;
    }

    /**
     * Check whether the array is associative or sequential.
     *
     * @param array $arr – array to check
     *
     * @return bool – true if the array is associative
     */
    private function isAssoc(array $arr) {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Expand shortified tool settings.
     *
     * @param $rule – tool settings
     *
     * @throws CElement_FormInput_EditorJs_EditorJsException
     *
     * @return array – expanded tool settings
     */
    private function expandToolSettings($rule) {
        if (is_string($rule)) {
            // 'blockName': 'string' – tool with string type and default settings
            $expandedRule = ['type' => $rule];
        } elseif (is_array($rule)) {
            if ($this->isAssoc($rule)) {
                $expandedRule = $rule;
            } else {
                // 'blockName': [] – tool with canBeOnly and default settings
                $expandedRule = ['type' => 'string', 'canBeOnly' => $rule];
            }
        } else {
            throw new CElement_FormInput_EditorJs_EditorJsException("Cannot determine element type of the rule `${rule}`.");
        }

        return $expandedRule;
    }
}
