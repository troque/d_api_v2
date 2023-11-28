<?php

namespace App\Observers;

use App\Models\DepartamentoModel;
use Illuminate\Support\Facades\Log;

class DepartamentoObserver
{
    /**
     * Handle the Department "created" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function created(DepartamentoModel $department)
    {
        $department->created_user = auth()->user()->name ?? "no user loger to create";
    }

    /**
     * Handle the Department "updated" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function updated(DepartamentoModel $department)
    {
        $department->updated_user = auth()->user()->name ?? "no user loger to update";
    }

    /**
     * Handle the Department "deleted" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function deleted(DepartamentoModel $department)
    {
        $department->deleted_user = auth()->user()->name ?? "no user loger to delete";
    }

    /**
     * Handle the Department "restored" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function restored(DepartamentoModel $department)
    {
        //
    }

    /**
     * Handle the Department "force deleted" event.
     *
     * @param  \App\Models\Department  $department
     * @return void
     */
    public function forceDeleted(DepartamentoModel $department)
    {
        //
    }
}
