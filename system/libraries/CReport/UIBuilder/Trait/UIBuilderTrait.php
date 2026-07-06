<?php

trait CReport_UIBuilder_Trait_UIBuilderTrait {
    /**
     * Build the report UI builder page.
     *
     * @return CApp
     */
    public function ui() {
        $app = c::app();

        $app->title('Report - UI Builder');
        $app->addView(CReport_UIBuilder::VIEW);

        return $app;
    }
}
