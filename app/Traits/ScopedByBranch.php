<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByBranch
{
    protected static function bootScopedByBranch()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            if (Auth::check() && Auth::user()->branch_id && !Auth::user()->hasAnyRole(['system_admin', 'pengurus', 'pengawas'])) {
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.branch_id', Auth::user()->branch_id);
            }
        });

        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->branch_id && empty($model->branch_id)) {
                $model->branch_id = Auth::user()->branch_id;
            }
        });
    }
}
