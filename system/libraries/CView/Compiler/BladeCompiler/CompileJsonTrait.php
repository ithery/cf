<?php

trait CView_Compiler_BladeCompiler_CompileJsonTrait {
    /**
     * The default JSON encoding options.
     *
     * @var int
     */
    private $encodingOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

    /**
     * Compile the JSON statement into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileJson($expression) {
        $parts = explode(',', $this->stripParentheses($expression));

        $options = isset($parts[1]) ? trim($parts[1]) : $this->encodingOptions;

        $depth = isset($parts[2]) ? trim($parts[2]) : 512;

        return "<?php echo json_encode({$parts[0]}, {$options}, {$depth}) ?>";
    }

    /**
     * Compile the JSON statement on attribute into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileJsonAttr($expression) {
        $parts = explode(',', $this->stripParentheses($expression));

        $options = isset($parts[1]) ? trim($parts[1]) : $this->encodingOptions;

        $depth = isset($parts[2]) ? trim($parts[2]) : 512;

        return "<?php echo htmlspecialchars(json_encode({$parts[0]}, {$options}, {$depth}), ENT_QUOTES, 'UTF-8') ?>";
    }
}
