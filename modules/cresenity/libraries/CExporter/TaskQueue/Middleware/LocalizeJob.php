<?php
class CExporter_TaskQueue_Middleware_LocalizeJob {
    use CTrait_Localizable;

    /**
     * @var object
     */
    private $localizable;

    /**
     * LocalizeJob constructor.
     *
     * @param object $localizable
     */
    public function __construct($localizable) {
        $this->localizable = $localizable;
    }

    /**
     * Handles the job.
     *
     * @param mixed   $job
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($job, Closure $next) {
        $locale = c::value(function () {
            if ($this->localizable instanceof CTranslation_Contract_HasLocalePreferenceInterface) {
                return $this->localizable->preferredLocale();
            }

            return null;
        });

        return $this->withLocale($locale, function () use ($next, $job) {
            return $next($job);
        });
    }
}
