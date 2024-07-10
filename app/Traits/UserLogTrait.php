<?php

namespace App\Traits;
use OwenIt\Auditing\Models\Audit;

trait UserLogTrait
{
    public function recordAuditEvent($event, $oldValues = [], $newValues = [])
    {
        $audit = new Audit();
        $audit->user_type = get_class($this);
        $audit->user_id = $this->id;
        $audit->event = $event;
        $audit->auditable_type  = get_class($this);
        $audit->auditable_id = $this->id;
        $audit->old_values = $oldValues;
        $audit->new_values = $newValues;
        $audit->url = request()->fullUrl();
        $audit->ip_address = request()->ip();
        $audit->user_agent = request()->userAgent();
        $audit->save();
    }
}
