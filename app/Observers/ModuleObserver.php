<?php

namespace App\Observers;

use App\Models\Module;
use Spatie\Permission\Models\Permission;

class ModuleObserver
{
    /**
     * Handle the Module "created" event.
     */
    public function created(Module $module): void
    {
        //
    }

    /**
     * Handle the Module "updated" event.
     */
    public function updated(Module $module): void
    {
        //
    }

    public function updating(Module $module): void
    {
        $oldModuleCode = $module->getOriginal('code');
        $module->actions->each(function ($action) use ($oldModuleCode, $module) {
            $permissionCode = $module->subsystem->system->code . '-' . $module->subsystem->code . '-' . $oldModuleCode . '-' . $action->code;
            $permission = Permission::where('code', $permissionCode)->first();
            if ($permission) {
                $newPermissionCode = $module->subsystem->system->code . '-' . $module->subsystem->code . '-' . $module->code . '-' . $action->code;
                $permission->code = $newPermissionCode;
                $permission->name = $newPermissionCode;
                $permission->save();
            }
        });

        $module->code = strtoupper($module->code);
    }

    /**
     * Handle the Module "deleted" event.
     */
    public function deleted(Module $module): void
    {
        //
    }

    /**
     * Handle the Module "restored" event.
     */
    public function restored(Module $module): void
    {
        //
    }

    /**
     * Handle the Module "force deleted" event.
     */
    public function forceDeleted(Module $module): void
    {
        //
    }

    public function deleting(Module $module): void
    {
        $module->actions()->delete();
    }
}
