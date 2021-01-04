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

        $source = null;
        try {
            $source = $this->findSource();
        } catch (Exception $ex) {
        }
        if ($source != null) {
            $trace = $this->prepareParams(c::collect($source)->map(function ($value) {
                return $value->name . ':' . $value->line;
            })->all());

            $params['trace'] = htmlentities($this->getDataFormatter()->formatVar($trace), ENT_QUOTES, 'UTF-8', false);
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
            if (is_object($value)) {
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

    /**
     * Use a backtrace to search for the origins of the events.
     *
     * @return array
     */
    protected function findSource() {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 50);

        $sources = [];

        foreach ($stack as $index => $trace) {
            $sources[] = $this->parseTrace($index, $trace);
        }

        return array_filter($sources);
    }

    /**
     * Parse a trace element from the backtrace stack.
     *
     * @param int   $index
     * @param array $trace
     *
     * @return object|bool
     */
    protected function parseTrace($index, array $trace) {
        $frame = (object) [
            'index' => $index,
            'namespace' => null,
            'name' => null,
            'line' => isset($trace['line']) ? $trace['line'] : '?',
        ];
        if (isset($trace['function']) && $trace['function'] == 'substituteBindings') {
            $frame->name = 'Route binding';
            return $frame;
        }
        if (isset($trace['class'])
            && isset($trace['file'])
            && !$this->fileIsInExcludedPath($trace['file'])
        ) {
            $file = $trace['file'];

            $frame->name = $this->normalizeFilename($file);
            return $frame;
        }
        return false;
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
