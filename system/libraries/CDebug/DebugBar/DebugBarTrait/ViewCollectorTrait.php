<?php

trait CDebug_DebugBar_DebugBarTrait_ViewCollectorTrait {
    /**
     * Create and setup ViewCollector.
     *
     * @return null|CDebug_DebugBar_DataCollector_ViewCollector
     */
    public function setupViewCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('view', true)) {
            try {
                $collectData = CF::config('debug.debugbar.options.views.data', true);
                $excludePaths = CF::config('debug.debugbar.options.views.exclude_paths', []);
                $viewCollector = new CDebug_DebugBar_DataCollector_ViewCollector($collectData, $excludePaths);
                $debugbar = $this;
                CEvent::dispatcher()->listen(
                    'composing:*',
                    function ($view, $data = []) use ($debugbar) {
                        if ($data) {
                            $view = $data[0]; // For Laravel >= 5.4
                        }
                        $debugbar['views']->addView($view);
                    }
                );
                $this->addCollector($viewCollector);

                return $viewCollector;
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add ViewCollector to CF Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        return null;
    }
}
