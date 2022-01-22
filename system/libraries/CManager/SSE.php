<?php

class CManager_SSE {
    public static function notify($clientId, $message, $type = 'info') {
        $sseModelClass = static::getModelClass();

        return $sseModelClass::saveEvent($clientId, $message, $type, 'message', $clientId);
    }

    public static function getModelClass() {
        $sseModelClass = CF::config('sse.log_model', CApp_Model_LogSSE::class);

        return $sseModelClass;
    }
}
