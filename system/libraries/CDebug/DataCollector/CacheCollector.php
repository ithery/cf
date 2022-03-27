<?php

class CDebug_DataCollector_CacheCollector extends CDebug_DataCollector_TimeDataCollector implements CDebug_DataCollector_AssetProviderInterface {
    /**
     * @var bool
     */
    protected $collectValues;

    /**
     * @var array
     */
    protected $classMap = [
        CCache_Event_CacheHit::class => 'hit',
        CCache_Event_CacheMissed::class => 'missed',
        CCache_Event_KeyWritten::class => 'written',
        CCache_Event_KeyForgotten::class => 'forgotten',
    ];

    public function __construct($requestStartTime = null, $collectValues = true) {
        parent::__construct();

        $this->collectValues = $collectValues;
    }

    public function onCacheEvent(CCache_EventAbstract $event) {
        $class = get_class($event);
        $params = get_object_vars($event);

        $label = $this->classMap[$class];

        if (isset($params['value'])) {
            if ($this->collectValues) {
                $params['value'] = htmlspecialchars($this->getDataFormatter()->formatVar($event->value));
            } else {
                unset($params['value']);
            }
        }

        if (!empty($params['key']) && in_array($label, ['hit', 'written'])) {
            $deleteUrl = 'cresenity/cache/delete?key=' . urlencode($params['key']);
            if (!empty($params['tags'])) {
                $deleteUrl .= '&tags=' . json_encode($params['tags']);
            }
            $params['delete'] = c::url($deleteUrl);
        }

        $time = microtime(true);
        $this->addMeasure($label . "\t" . $event->key, $time, $time, $params);
    }

    public function subscribe(CEvent_Dispatcher $dispatcher) {
        foreach ($this->classMap as $eventClass => $type) {
            $dispatcher->listen($eventClass, [$this, 'onCacheEvent']);
        }
    }

    public function collect() {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName() {
        return 'cache';
    }

    public function getAssets() {
        return [
            'js' => ['debug/debugbar/widgets/cache/widget.js']
        ];
    }

    public function getWidgets() {
        return [
            'cache' => [
                'icon' => 'clipboard',
                'widget' => 'PhpDebugBar.Widgets.CacheWidget',
                'map' => 'cache',
                'default' => '{}',
            ],
            'cache:badge' => [
                'map' => 'cache.nb_measures',
                'default' => 'null',
            ],
        ];
    }
}
