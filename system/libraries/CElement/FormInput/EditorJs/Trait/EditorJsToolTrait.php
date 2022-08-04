<?php

/**
 * Class BlockHandler.
 */
trait CElement_FormInput_EditorJs_Trait_EditorJsToolTrait {
    protected $tools;

    /**
     * @return CElement_FormInput_EditorJs_Tool_HeaderTool
     */
    public function headerTool() {
        return $this->tools['header'];
    }

    /**
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withHeaderTool(Closure $callback) {
        $callback($this->headerTool());

        return $this;
    }

    /**
     * @return CElement_FormInput_EditorJs_Tool_ChecklistTool
     */
    public function checklistTool() {
        return $this->tools['checklist'];
    }

    /**
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withChecklistTool(Closure $callback) {
        $callback($this->checklistTool());

        return $this;
    }

    /**
     * @return CElement_FormInput_EditorJs_Tool_LinkTool
     */
    public function linkTool() {
        return $this->tools['link'];
    }

    /**
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withLinkTool(Closure $callback) {
        $callback($this->linkTool());

        return $this;
    }

    /**
     * @return CElement_FormInput_EditorJs_Tool_ImageTool
     */
    public function imageTool() {
        return $this->tools['image'];
    }

    /**
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withImageTool(Closure $callback) {
        $callback($this->imageTool());

        return $this;
    }

    /**
     * @return CElement_FormInput_EditorJs_Tool_RawTool
     */
    public function rawTool() {
        return $this->tools['raw'];
    }

    /**
     * @param Closure $callback
     *
     * @return CElement_FormInput_EditorJs
     */
    public function withRawTool(Closure $callback) {
        $callback($this->rawTool());

        return $this;
    }
}
