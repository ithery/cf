<?php

/**
 * @internal
 */
interface CQC_Phpstan_Contract_Method_PipeInterface {
    /**
     * @param \CQC_Phpstan_Contract_Method_PassableInterface $passable
     * @param \Closure                                       $next
     *
     * @return void
     */
    public function handle(CQC_Phpstan_Contract_Method_PassableInterface $passable, Closure $next): void;
}
