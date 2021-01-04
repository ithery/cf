<?php

/**
 * Collector for Models.
 */
class CDebug_DataCollector_ModelCollector extends CDebug_DataCollector implements CDebug_Bar_Interface_RenderableInterface {
    public $models = [];
    public $count = 0;

    public function __construct() {
        CEvent::dispatcher()->listen('model.retrieved:*', function ($event, $models) {
            foreach (array_filter($models) as $model) {
                $class = get_class($model);

                $this->models[$class] = (carr::get($this->models, $class, 0)) + 1;
                $this->count++;
            }
        });
    }

    public function collect() {
        ksort($this->models, SORT_NUMERIC);

        return ['data' => array_reverse($this->models), 'count' => $this->count];
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return 'models';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets() {
        return [
            'models' => [
                'icon' => 'cubes',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'models.data',
                'default' => '{}'
            ],
            'models:badge' => [
                'map' => 'models.count',
                'default' => 0
            ]
        ];
    }
}
