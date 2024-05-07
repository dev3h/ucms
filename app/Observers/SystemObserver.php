<?php

namespace App\Observers;

use App\Models\System;
use Spatie\Permission\Models\Permission;

class SystemObserver
{
    /**
     * Handle the System "created" event.
     */
    public function created(System $system): void
    {
        //
    }

    /**
     * Handle the System "updated" event.
     */
    public function updated(System $system): void
    {

    }

    public function updating(System $system): void
    {
        $oldSystemCode = $system->getOriginal('code');

        // Update all permissions that contain the old system code
        $system?->subsystems?->each(function ($subsystem) use ($oldSystemCode, $system) {
            $subsystem?->modules?->each(function ($module) use ($subsystem, $oldSystemCode, $system) {
                $module?->actions?->each(function ($action) use ($module, $subsystem, $oldSystemCode, $system) {
                    $permissionCode = $oldSystemCode . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
                    $permission = Permission::where('code', $permissionCode)->first();
                    if ($permission) {
                        $newPermissionCode = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
                        $permission->code = $newPermissionCode;
                        $permission->name = $newPermissionCode;
                        $permission->save();
                    }
                });
            });
        });

        // Update the system code
        $system->code = strtoupper($system->code);
    }

    /**
     * Handle the System "deleted" event.
     */
    public function deleted(System $system): void
    {
        //
    }

    /**
     * Handle the System "restored" event.
     */
    public function restored(System $system): void
    {
        //
    }

    /**
     * Handle the System "force deleted" event.
     */
    public function forceDeleted(System $system): void
    {
        //
    }

    public function deleting(System $system): void
    {
        $system->subsystems()->delete();
    }
}
