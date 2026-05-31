<?php

namespace App\Observers;

use App\Models\Assessment;
use App\Models\AuditLog;

class AssessmentObserver
{
    /**
     * Handle the Assessment "created" event.
     */
    public function created(Assessment $assessment): void
    {
        AuditLog::record(
            action: 'created',
            tableName: 'assessments',
            recordId: $assessment->id,
            newData: $assessment->toArray(),
        );
    }

    /**
     * Handle the Assessment "updated" event.
     */
    public function updated(Assessment $assessment): void
    {
        AuditLog::record(
            action: 'updated',
            tableName: 'assessments',
            recordId: $assessment->id,
            oldData: $assessment->getOriginal(),
            newData: $assessment->toArray(),
        );
    }

    /**
     * Handle the Assessment "deleted" event.
     */
    public function deleted(Assessment $assessment): void
    {
        AuditLog::record(
            action: 'deleted',
            tableName: 'assessments',
            recordId: $assessment->id,
            oldData: $assessment->toArray(),
        );
    }

    /**
     * Handle the Assessment "restored" event.
     */
    public function restored(Assessment $assessment): void
    {
        //
    }

    /**
     * Handle the Assessment "force deleted" event.
     */
    public function forceDeleted(Assessment $assessment): void
    {
        //
    }
}
