<?php
class CHouseKeeping_Database_LogActivity {

    /**
     * Execute the housekeeping process to delete old log activity records.
     *
     * @param int $keepDays The number of days to keep log activity records. Records older than this will be deleted.
     *
     * @return bool Returns true if the housekeeping process was successful.
     */
    public static function execute($keepDays = 365) {
        $modelName = CF::config('app.model.log_activity', CApp_Model_LogActivity::class);
        $bottomDate = c::now()->subDays($keepDays);
        $query = $modelName::query()->where('created', '<', $bottomDate);
        $query->forceDelete();
        return true;
    }
}
