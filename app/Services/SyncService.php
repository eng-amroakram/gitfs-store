<?php

namespace App\Services;

use App\Models\SyncLogs;

class SyncService
{
    public function getUnsyncedUsers($modelClass, $columns = ['*'])
    {
        return $modelClass::whereNotIn('id', function ($query) use ($modelClass) {
            $query->select('syncable_id')
                ->from('sync_logs')
                ->where('syncable_type', $modelClass);
        })->select($columns)->get();
    }

    public function confirmUsersSync($modelClass, array $ids)
    {
        $count = 0;

        foreach ($ids as $id) {
            $log = SyncLogs::updateOrCreate(
                [
                    'user_id' => 1,
                    'syncable_id' => $id,
                    'syncable_type' => $modelClass,
                ],
                [
                    'synced_at' => now()
                ]
            );
            if ($log) $count++;
        }

        return $count;
    }

    /**
     * جلب السجلات الغير مزامنة مع المستخدم
     */
    public function getUnsynced($userId, $modelClass, $columns = ['*'])
    {
        return $modelClass::whereNotIn('id', function ($query) use ($userId, $modelClass) {
            $query->select('syncable_id')
                ->from('sync_logs')
                ->where('user_id', $userId)
                ->where('syncable_type', $modelClass);
        })->select($columns)->get();
    }

    /**
     * تأكيد المزامنة
     */
    public function confirmSync($userId, $modelClass, array $ids)
    {
        $count = 0;

        foreach ($ids as $id) {
            $log = SyncLogs::updateOrCreate(
                [
                    'user_id' => $userId,
                    'syncable_id' => $id,
                    'syncable_type' => $modelClass,
                ],
                [
                    'synced_at' => now()
                ]
            );
            if ($log) $count++;
        }

        return $count;
    }

    /**
     * تسجيل سجل مزامنة لمستخدم
     */
    public function logSync($userId, $model)
    {
        return SyncLogs::updateOrCreate(
            [
                'user_id' => $userId,
                'syncable_id' => $model->id,
                'syncable_type' => get_class($model),
            ],
            [
                'synced_at' => now()
            ]
        );
    }
}
