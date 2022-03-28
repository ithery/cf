<?php

class CDebug_Collector_Deprecated extends CDebug_Collector_Exception {
    public function collect($message = '') {
        static $totalDeprecated = 0;
        $totalDeprecated++;

        if ($totalDeprecated < 10) {
            try {
                throw new Exception($message);
            } catch (Exception $ex) {
                $trace = $ex->getTrace();

                $stack1 = carr::get($trace, 1);
                $stack2 = carr::get($trace, 2);

                $func1 = isset($stack1['class']) ? $stack1['class'] . '::' : '';
                $func1 .= carr::get($stack1, 'function');
                $func1 .= isset($stack1['file']) ? ' at file' . $stack1['file'] : '';
                $func1 .= isset($stack1['line']) ? '[' . $stack1['line'] . ']' : '';

                $func2 = isset($stack2['class']) ? $stack2['class'] . '::' : '';
                $func2 .= carr::get($stack2, 'function');
                $func2 .= isset($stack2['file']) ? ' at file' . $stack2['file'] : '';
                $func2 .= isset($stack2['line']) ? '[' . $stack2['line'] . ']' : '';

                $messageDeprecated = 'Deprecated:' . $func1 . ' called in ' . $func2;

                $data = $this->getDataFromException($ex);
                $data['message'] = $data['message'] . ':' . $messageDeprecated;
                $this->put($data);
                unset($ex);
            }
        }
    }

    public function getType() {
        return CDebug::COLLECTOR_TYPE_DEPRECATED;
    }
}
