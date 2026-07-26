<?php

/**
 * Fluent accessors for the EditorJS tool instances registered on
 * {@see CElement_FormInput_EditorJs}.
 */
trait CElement_FormInput_EditorJs_Trait_EditorJsToolTrait {
    /**
     * Tool instances keyed by tool name.
     *
     * @var CElement_FormInput_EditorJs_ToolAbstract[]
     */
    protected $tools;

    /**
     * Get the header tool instance.
     *
     * @return CElement_FormInput_EditorJs_Tool_HeaderTool
     */
    public function headerTool() {
        return $this->tools['header'];
    }

    /**
     * Configure the header tool via a callback.
     *
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withHeaderTool(Closure $callback) {
        $callback($this->headerTool());

        return $this;
    }

    /**
     * Get the checklist tool instance.
     *
     * @return CElement_FormInput_EditorJs_Tool_ChecklistTool
     */
    public function checklistTool() {
        return $this->tools['checklist'];
    }

    /**
     * Configure the checklist tool via a callback.
     *
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withChecklistTool(Closure $callback) {
        $callback($this->checklistTool());

        return $this;
    }

    /**
     * Get the link tool instance.
     *
     * @return CElement_FormInput_EditorJs_Tool_LinkTool
     */
    public function linkTool() {
        return $this->tools['link'];
    }

    /**
     * Configure the link tool via a callback.
     *
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withLinkTool(Closure $callback) {
        $callback($this->linkTool());

        return $this;
    }

    /**
     * Get the image tool instance.
     *
     * @return CElement_FormInput_EditorJs_Tool_ImageTool
     */
    public function imageTool() {
        return $this->tools['image'];
    }

    /**
     * Configure the image tool via a callback.
     *
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withImageTool(Closure $callback) {
        $callback($this->imageTool());

        return $this;
    }

    /**
     * Get the raw HTML tool instance.
     *
     * @return CElement_FormInput_EditorJs_Tool_RawTool
     */
    public function rawTool() {
        return $this->tools['raw'];
    }

    /**
     * Configure the raw HTML tool via a callback.
     *
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withRawTool(Closure $callback) {
        $callback($this->rawTool());

        return $this;
    }
}
