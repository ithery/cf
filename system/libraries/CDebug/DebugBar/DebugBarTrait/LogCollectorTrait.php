<?php

use DebugBar\DataCollector\MessagesCollector;

trait CDebug_DebugBar_DebugBarTrait_LogCollectorTrait {
    /**
     * Create and setup FilesCollector.
     *
     * @return null|MessagesCollector
     */
    public function setupLogCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('log', true)) {
            $logCollector = new MessagesCollector();
            $this->addCollector($logCollector);
            CEvent::dispatcher()->listen(CLogger_Event_MessageLogged::class, function ($level, $message = null, $context = null) use ($logCollector) {
                // Laravel 5.4 changed how the global log listeners are called. We must account for
                // the first argument being an "event object", where arguments are passed
                // via object properties, instead of individual arguments.
                if ($level instanceof \CLogger_Event_MessageLogged) {
                    $message = $level->message;
                    $context = $level->context;
                    $level = $level->level;
                }

                try {
                    $logMessage = (string) $message;
                    if (mb_check_encoding($logMessage, 'UTF-8')) {
                        $logMessage .= (!empty($context) ? ' ' . json_encode($context, JSON_PRETTY_PRINT) : '');
                    } else {
                        $logMessage = '[INVALID UTF-8 DATA]';
                    }
                } catch (\Exception $e) {
                    $logMessage = '[Exception: ' . $e->getMessage() . ']';
                }
                $logCollector->addMessage(
                    '[' . date('H:i:s') . '] ' . "LOG.$level: " . $logMessage,
                    $level,
                    false
                );
            });

            return $logCollector;
        }

        return null;
    }
}
