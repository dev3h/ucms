<?php

namespace App\Observers;

use App\Models\Action;
use Spatie\Permission\Models\Permission;

class ActionObserver
{
    /**
     * Handle the Action "created" event.
     */
    public function created(Action $action): void
    {
        //
    }

    /**
     * Handle the Action "updated" event.
     */
    public function updated(Action $action): void
    {
        //
    }

    public function updating(Action $action): void
    {
        $oldActionCode = $action->getOriginal('code');
        $system = $action->module->subsystem->system;
        $subsystem = $action->module->subsystem;
        $module = $action->module;
        $permissionCode = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $oldActionCode;
        $permission = Permission::where('code', $permissionCode)->first();
        if ($permission) {
            $newPermissionCode = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
            $permission->code = $newPermissionCode;
            $permission->name = $newPermissionCode;
            $permission->save();
        }

        $action->code = strtoupper($action->code);
    }

    /**
     * Handle the Action "deleted" event.
     */
    public function deleted(Action $action): void
    {
        //
    }

    /**
     * Handle the Action "restored" event.
     */
    public function restored(Action $action): void
    {
        //
    }

    /**
     * Handle the Action "force deleted" event.
     */
    public function forceDeleted(Action $action): void
    {
        //
    }

    public function deleting(Action $action): void
    {
        $system = $action->module->subsystem->system;
        $subsystem = $action->module->subsystem;
        $module = $action->module;
        $permissionCode = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
        $permission = Permission::where('code', $permissionCode)->first();
        if ($permission) {
            $permission->delete();
        }
    }
}
