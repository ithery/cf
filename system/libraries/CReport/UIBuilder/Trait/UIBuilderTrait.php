<?php

trait CReport_UIBuilder_Trait_UIBuilderTrait {
    /**
     * Build the report UI builder page.
     *
     * @param array $data optional view data:
     *                    - datasets: array of ['name' => string, 'fields' => string[]], e.g. from CReport_DatasetRegistry::describe()
     *                    - previewUrl: url to POST jrxml + dataset for pdf preview
     *
     * @return CApp
     */
    public function ui(array $data = []) {
        $app = c::app();

        $app->title('Report - UI Builder');
        $app->addView(CReport_UIBuilder::VIEW, $data);

        return $app;
    }
}
