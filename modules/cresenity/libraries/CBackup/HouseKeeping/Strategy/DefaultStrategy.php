<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Carbon\Carbon;

class CBackup_HouseKeeping_Strategy_DefaultStrategy extends CBackup_HouseKeeping_AbstractStrategy {

    /** @var \CBackup_Record */
    protected $newestBackup;

    public function deleteOldBackups(CBackup_RecordCollection $backups) {
        // Don't ever delete the newest backup.
        $this->newestBackup = $backups->shift();

        $dateRanges = $this->calculateDateRanges();

        $backupsPerPeriod = $dateRanges->map(function (CBackup_HouseKeeping_Period $period) use ($backups) {
            return $backups->filter(function (CBackup_Record $backup) use ($period) {
                        return $backup->date()->between($period->startDate(), $period->endDate());
                    });
        });

        $backupsPerPeriod['daily'] = $this->groupByDateFormat($backupsPerPeriod['daily'], 'Ymd');
        $backupsPerPeriod['weekly'] = $this->groupByDateFormat($backupsPerPeriod['weekly'], 'YW');
        $backupsPerPeriod['monthly'] = $this->groupByDateFormat($backupsPerPeriod['monthly'], 'Ym');
        $backupsPerPeriod['yearly'] = $this->groupByDateFormat($backupsPerPeriod['yearly'], 'Y');


        
        $this->removeBackupsForAllPeriodsExceptOne($backupsPerPeriod);

        $this->removeBackupsOlderThan($dateRanges['yearly']->endDate(), $backups);

        $this->removeOldBackupsUntilUsingLessThanMaximumStorage($backups);
    }

    /**
     * 
     * @return CCollection
     */
    protected function calculateDateRanges() {
        $config = CBackup::getConfig('house_keeping.default_strategy');



        $daily = new CBackup_HouseKeeping_Period(
                Carbon::now()->subDays($config['keep_all_backups_for_days']), Carbon::now()
                        ->subDays($config['keep_all_backups_for_days'])
                        ->subDays($config['keep_daily_backups_for_days'])
        );

        $weekly = new CBackup_HouseKeeping_Period(
                $daily->endDate(), $daily->endDate()
                        ->subWeeks($config['keep_weekly_backups_for_weeks'])
        );

        $monthly = new CBackup_HouseKeeping_Period(
                $weekly->endDate(), $weekly->endDate()
                        ->subMonths($config['keep_monthly_backups_for_months'])
        );

        $yearly = new CBackup_HouseKeeping_Period(
                $monthly->endDate(), $monthly->endDate()
                        ->subYears($config['keep_yearly_backups_for_years'])
        );

        return c::collect(compact('daily', 'weekly', 'monthly', 'yearly'));
    }

    /**
     * 
     * @param CCollection $backups
     * @param string $dateFormat
     * @return CCollection
     */
    protected function groupByDateFormat(CCollection $backups, $dateFormat) {
        return $backups->groupBy(function (CBackup_Record $backup) use ($dateFormat) {
                    return $backup->date()->format($dateFormat);
                });
    }

    protected function removeBackupsForAllPeriodsExceptOne(CCollection $backupsPerPeriod) {
        $backupsPerPeriod->each(function (CCollection $groupedBackupsByDateProperty, $periodName) {
            $groupedBackupsByDateProperty->each(function (CCollection $group) {
                $group->shift();

                $group->each->delete();
            });
        });
    }

    protected function removeBackupsOlderThan(Carbon $endDate, CBackup_RecordCollection $backups) {
        $backups->filter(function (CBackup_Record $backup) use ($endDate) {
            return $backup->exists() && $backup->date()->lt($endDate);
        })->each->delete();
    }

    protected function removeOldBackupsUntilUsingLessThanMaximumStorage(CBackup_RecordCollection $backups) {
        if (!$oldest = $backups->oldest()) {
            return;
        }

        $maximumSize = CBackup::getConfig('house_keeping.default_strategy.delete_oldest_backups_when_using_more_megabytes_than') * 1024 * 1024;

        if (($backups->size() + $this->newestBackup->size()) <= $maximumSize) {
            return;
        }
        $oldest->delete();

        $backups = $backups->filter->exists();

        $this->removeOldBackupsUntilUsingLessThanMaximumStorage($backups);
    }

}
