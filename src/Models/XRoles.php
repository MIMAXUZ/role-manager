<?php

namespace MIMAXUZ\LRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class XRoles extends Model
{
    
    //Role Management
    public $timestamps = false;

    public function permissions()
    {
        return $this->belongsToMany(XPermissions::class, 'roles_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }
}
