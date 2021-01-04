<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 4:53:08 PM
 */

use Symfony\Component\VarDumper\Cloner\VarCloner;

class CDebug_DataCollector_EventCollector extends CDebug_DataCollector_TimeDataCollector {
    /**
     * @var CEvent_Dispatcher
     */
    protected $events;

    /**
     * @var integer
     */
    protected $previousTime;

    public function __construct($requestStartTime = null) {
        parent::__construct($requestStartTime);
        $this->previousTime = microtime(true);
        $this->setDataFormatter(new CDebug_DataFormatter_SimpleFormatter());
        $this->subscribe(CEvent::dispatcher());
    }

    public function onWildcardEvent($name = null, $data = []) {
        $params = $this->prepareParams($data);
        $currentTime = microtime(true);

        // Find all listeners for the current event
        foreach ($this->events->getListeners($name) as $i => $listener) {
            // Check if it's an object + method name
            if (is_array($listener) && count($listener) > 1 && is_object($listener[0])) {
                list($class, $method) = $listener;

                // Skip this class itself
                if ($class instanceof static) {
                    continue;
                }

                // Format the listener to readable format
                $listener = get_class($class) . '@' . $method;
            } elseif ($listener instanceof \Closure) {
                // Handle closures
                $reflector = new \ReflectionFunction($listener);

                // Skip our own listeners
                if ($reflector->getNamespaceName() == 'Barryvdh\Debugbar') {
                    continue;
                }

                // Format the closure to a readable format
                $filename = ltrim(str_replace(DOCROOT, '', $reflector->getFileName()), '/');
                $lines = $reflector->getStartLine() . '-' . $reflector->getEndLine();
                $listener = $reflector->getName() . ' (' . $filename . ':' . $lines . ')';
            } else {
                // Not sure if this is possible, but to prevent edge cases
                $listener = $this->getDataFormatter()->formatVar($listener);
            }

            $params['listeners.' . $i] = $listener;
        }
        $this->addMeasure($name, $this->previousTime, $currentTime, $params);
        $this->previousTime = $currentTime;
    }

    public function subscribe(CEvent_Dispatcher $events) {
        $this->events = $events;
        $events->listen('*', [$this, 'onWildcardEvent']);
    }

    protected function prepareParams($params) {
        $data = [];
        foreach ($params as $key => $value) {
            if (is_object($value) && cstr::is('Illuminate\*\Events\*', get_class($value))) {
                $value = $this->prepareParams(get_object_vars($value));
            }
            $data[$key] = htmlentities($this->getDataFormatter()->formatVar($value), ENT_QUOTES, 'UTF-8', false);
        }
        return $data;
    }

    public function collect() {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);
        return $data;
    }

    public function getName() {
        return 'event';
    }

    public function getWidgets() {
        return [
            'events' => [
                'icon' => 'tasks',
                'widget' => 'PhpDebugBar.Widgets.TimelineWidget',
                'map' => 'event',
                'default' => '{}',
            ],
            'events:badge' => [
                'map' => 'event.nb_measures',
                'default' => 0,
            ],
        ];
    }
}
