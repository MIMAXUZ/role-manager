<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use MIMAXUZ\LRoles\Models\XPermissions;

class ListPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:list
                            {--with-roles : Show roles that have each permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $query = XPermissions::query();

        if ($this->option('with-roles')) {
            $query->with('roles');
        }

        $permissions = $query->get();

        if ($permissions->isEmpty()) {
            $this->warn('No permissions found.');
            $this->line('Use "php artisan permission:create" to create a new permission.');
            return Command::SUCCESS;
        }

        if ($this->option('with-roles')) {
            foreach ($permissions as $permission) {
                $this->info("Permission: {$permission->name} (slug: {$permission->slug})");

                $roles = $permission->roles->pluck('slug')->toArray();
                $this->line('  Roles: ' . (empty($roles) ? 'None' : implode(', ', $roles)));
                $this->newLine();
            }
        } else {
            $this->table(
                ['ID', 'Name', 'Slug', 'Roles Count'],
                $permissions->map(fn($p) => [
                    $p->id,
                    $p->name,
                    $p->slug,
                    $p->roles()->count(),
                ])->toArray()
            );
        }

        return Command::SUCCESS;
    }
}
