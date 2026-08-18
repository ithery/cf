<?php

/**
 * Base class for EditorJS tool configuration objects.
 *
 * Each concrete subclass (see the `Tool` sub-namespace) represents the
 * server-side configuration for one EditorJS block/inline tool and produces
 * its JS-side config via {@see getConfig()}.
 */
abstract class CElement_FormInput_EditorJs_ToolAbstract {
    /**
     * Whether this tool is enabled in the editor.
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Enable this tool.
     *
     * @return void
     */
    public function enable() {
        $this->enabled = true;
    }

    /**
     * Disable this tool.
     *
     * @return void
     */
    public function disable() {
        $this->enabled = false;
    }

    /**
     * Determine whether this tool is enabled.
     *
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * Build the JS-side configuration array for this tool.
     *
     * @return array
     */
    abstract public function getConfig();
}
