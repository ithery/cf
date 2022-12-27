<?php

/**
 * @internal
 */
final class CQC_Phpstan_Service_Method_Pipe_SelfClass implements CQC_Phpstan_Contract_Method_PipeInterface {
    /**
     * @inheritdoc
     */
    public function handle(CQC_Phpstan_Contract_Method_PassableInterface $passable, Closure $next): void {
        $className = $passable->getClassReflection()
            ->getName();

        if (!$passable->searchOn($className)) {
            $next($passable);
        }
    }
}
