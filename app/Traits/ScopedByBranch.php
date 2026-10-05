<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByBranch
{
    protected static function bootScopedByBranch()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            if (Auth::check() && Auth::user()->branch_id && !Auth::user()->hasRole('system_admin')) {
                // For tables that have branch_id, restrict it.
                // If it's a join or something, we should specify the table name.
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.branch_id', Auth::user()->branch_id);
            }
        });
    }
}
