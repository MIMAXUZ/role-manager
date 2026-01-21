<?php

namespace MIMAXUZ\LRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class XRoles extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'x_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'slug'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the permissions that belong to the role.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            XPermissions::class,
            'roles_permissions',
            'x_roles_id',
            'x_permissions_id'
        );
    }

    /**
     * Get the users that belong to the role.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $userModel = config('role-manager.user_model', 'App\\Models\\User');

        return $this->belongsToMany(
            $userModel,
            'users_roles',
            'x_roles_id',
            'user_id'
        );
    }

    /**
     * Find a role by its slug.
     *
     * @param string $slug
     * @return static|null
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
