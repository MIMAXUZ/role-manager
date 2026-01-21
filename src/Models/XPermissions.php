<?php

namespace MIMAXUZ\LRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class XPermissions extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'x_permissions';

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
     * Get the roles that have this permission.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            XRoles::class,
            'roles_permissions',
            'x_permissions_id',
            'x_roles_id'
        );
    }

    /**
     * Find a permission by its slug.
     *
     * @param string $slug
     * @return static|null
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
