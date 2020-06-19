<?php

namespace MIMAXUZ\LRoles\Models;

use Illuminate\Database\Eloquent\Model;

class XPermissions extends Model
{
    //Permission Management System

    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(XRoles::class, 'roles_permissions');
    }
}
