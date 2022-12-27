<?php
class CHouseKeeping_Database_LogActivity {
    public static function execute($keepDays = 365) {
        $modelName = CF::config('app.model.log_activity', CApp_Model_LogActivity::class);
        $bottomDate = c::now()->subDays($keepDays);
        $query = $modelName::query()->where('created', '<', $bottomDate);
        $query->forceDelete();
    }
}
