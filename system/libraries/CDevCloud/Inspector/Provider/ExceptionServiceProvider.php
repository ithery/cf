<?php

class CDevCloud_Inspector_Provider_ExceptionServiceProvider extends CDevCloud_Inspector_ProviderAbstract {
    public function boot() {
        CEvent::dispatcher()->listen(CLogger_Event_MessageLogged::class, function (CLogger_Event_MessageLogged $log) {
            $this->handleLog($log->level, $log->message, $log->context);
        });
    }

    /**
     * Attach the event to the current transaction.
     *
     * @param string $level
     * @param mixed  $message
     * @param mixed  $context
     *
     * @return mixed
     */
    protected function handleLog($level, $message, $context) {
        if (isset($context['exception'])
            && ($context['exception'] instanceof \Exception || $context['exception'] instanceof \Throwable)
        ) {
            return $this->reportException($context['exception']);
        }

        if ($message instanceof \Exception || $message instanceof \Throwable) {
            return $this->reportException($message);
        }

        // Collect general log messages
        if (CDevCloud::inspector()->isRecording() && CDevCloud::inspector()->hasTransaction()) {
            CDevCloud::inspector()->transaction()
                ->addContext('logs', array_merge(
                    CDevCloud::inspector()->transaction()->getContext()['logs'] ?? [],
                    [
                        compact('level', 'message')
                    ]
                ));
        }
    }

    protected function reportException(Throwable $exception) {
        if (!CDevCloud::inspector()->isRecording()) {
            return;
        }

        if (CDevCloud::inspector()->needTransaction()) {
            CDevCloud::inspector()->startTransaction(get_class($exception));
        }

        CDevCloud::inspector()->reportException($exception, false);
        CDevCloud::inspector()->transaction()->setResult('error');
    }
}
