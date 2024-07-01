<?php

namespace App\Services;

use Spatie\Activitylog\ActivityLogger as SpatieActivityLogger;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class ActivityLoggerService extends SpatieActivityLogger
{
    protected $logType;

    public function logType(string $logType): self
    {
        $this->logType = $logType;

        return $this;
    }

    public function log($description): ?ActivityContract
    {
        $activity = parent::log($description);

        if ($this->logType) {
            $activity->log_type = $this->logType;
            $activity->save();
        }

        return $activity;
    }
}
