<?php

namespace App\Observers;

use App\Models\SubSystem;
use Spatie\Permission\Models\Permission;

class SubSystemObserver
{
    /**
     * Handle the SubSystem "created" event.
     */
    public function created(SubSystem $subSystem): void
    {
        //
    }

    /**
     * Handle the SubSystem "updated" event.
     */
    public function updated(SubSystem $subSystem): void
    {
        //
    }

    public function updating(SubSystem $subSystem): void
    {
        $oldSubSystemCode = $subSystem->getOriginal('code');
        $subSystem->modules->each(function ($module) use ($oldSubSystemCode, $subSystem) {
            $module->actions->each(function ($action) use ($module, $oldSubSystemCode, $subSystem) {
                $permissionCode = $subSystem->system->code . '-' . $oldSubSystemCode . '-' . $module->code . '-' . $action->code;
                $permission = Permission::where('code', $permissionCode)->first();
                if ($permission) {
                    $newPermissionCode = $subSystem->system->code . '-' . $subSystem->code . '-' . $module->code . '-' . $action->code;
                    $permission->code = $newPermissionCode;
                    $permission->name = $newPermissionCode;
                    $permission->save();
                }
            });
        });

        $subSystem->code = strtoupper($subSystem->code);
    }

    /**
     * Handle the SubSystem "deleted" event.
     */
    public function deleted(SubSystem $subSystem): void
    {
        //
    }

    /**
     * Handle the SubSystem "restored" event.
     */
    public function restored(SubSystem $subSystem): void
    {
        //
    }

    /**
     * Handle the SubSystem "force deleted" event.
     */
    public function forceDeleted(SubSystem $subSystem): void
    {
        //
    }

    public function deleting(SubSystem $subSystem): void
    {
        $subSystem->modules()->delete();
    }
}
