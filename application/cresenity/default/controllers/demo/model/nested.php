<?php

class Controller_Demo_Model_Nested extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Nested Model');

        $app->addDiv()->addClass('mb-3')->add(
            'This demo uses <code>CModel_Nested_NestedTrait</code> (a nested-set model, storing '
            . '<code>lft</code>/<code>rgt</code>/<code>depth</code>/<code>parent_id</code>) to store and query hierarchical data. '
            . 'The table below is ordered by <code>lft</code> and the name is indented by <code>depth</code>.'
        );

        $table = $app->addTable();
        $table->setDataFromModel(\Cresenity\Demo\Model\NestedCategory::class, function ($query) {
            $query->orderBy('lft');
        });
        $table->addColumn('nested_category_id')->setLabel('ID');
        $table->addColumn('name')->setLabel('Name')->setCallback(function ($row, $value) {
            return str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', carr::get($row, 'depth')) . c::e($value);
        });
        $table->addColumn('depth')->setLabel('Depth');
        $table->addColumn('lft')->setLabel('Lft');
        $table->addColumn('rgt')->setLabel('Rgt');
        $table->setAjax();

        $this->descendantsExample($app);
        $this->ancestorsExample($app);

        return $app;
    }

    /**
     * @param CApp $app
     */
    private function descendantsExample($app) {
        $widget = $app->addWidget()->setTitle('descendants() - all nodes under "Electronics"');

        $electronics = \Cresenity\Demo\Model\NestedCategory::where('name', 'Electronics')->first();
        $names = $electronics->descendants()->orderBy('lft')->get()->pluck('name')->all();

        $widget->addDiv()->add(implode(', ', array_map('c::e', $names)));
    }

    /**
     * @param CApp $app
     */
    private function ancestorsExample($app) {
        $widget = $app->addWidget()->setTitle('ancestors() - breadcrumb for "Laptops"');

        $laptops = \Cresenity\Demo\Model\NestedCategory::where('name', 'Laptops')->first();
        $names = $laptops->ancestors()->orderBy('lft')->get()->pluck('name')->all();
        $names[] = $laptops->name;

        $widget->addDiv()->add(implode(' &raquo; ', array_map('c::e', $names)));
    }
}
