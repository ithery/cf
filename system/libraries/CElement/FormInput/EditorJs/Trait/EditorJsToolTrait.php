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
     * @return CElement_FormInput_EditorJs
     */
    public function withChecklistTool(Closure $callback) {
        $callback($this->checklistTool());

        return $this;
    }
}
