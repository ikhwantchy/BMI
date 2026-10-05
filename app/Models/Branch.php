<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'address'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
