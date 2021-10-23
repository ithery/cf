<?php
interface CException_Contract_HasSolutionsForThrowableInterface {
    /**
     * @param Throwable $throwable
     */
    public function canSolve($throwable);

    /**
     * \CException_Contract_SolutionInterface[].
     *
     * @param Throwable $throwable
     *
     * @return CException_Contract_SolutionInterface[]
     */
    public function getSolutions($throwable);
}
