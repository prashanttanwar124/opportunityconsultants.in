<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model): void {
            $label = method_exists($model, 'activityLabel')
                ? $model->activityLabel()
                : class_basename($model).' #'.$model->getKey();

            ActivityLog::record('created', "Created {$label}", $model);
        });

        static::updated(function (Model $model): void {
            $changes = array_keys($model->getChanges());
            $meaningful = array_values(array_diff($changes, ['updated_at', 'remember_token']));

            if ($meaningful === []) {
                return;
            }

            $label = method_exists($model, 'activityLabel')
                ? $model->activityLabel()
                : class_basename($model).' #'.$model->getKey();

            ActivityLog::record('updated', "Updated {$label}", $model, [
                'fields' => $meaningful,
            ]);
        });

        static::deleted(function (Model $model): void {
            $label = method_exists($model, 'activityLabel')
                ? $model->activityLabel()
                : class_basename($model).' #'.$model->getKey();

            ActivityLog::record('deleted', "Deleted {$label}", $model);
        });
    }
}
