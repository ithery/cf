<?php

/**
 * Description of ReorderableInterface
 *
 * @author Hery
 */
interface CQC_ReorderableInterface {

    public function sortId();

    /**
     * @return list<ExecutionOrderDependency>
     */
    public function provides();

    /**
     * @return list<ExecutionOrderDependency>
     */
    public function requires();
}
