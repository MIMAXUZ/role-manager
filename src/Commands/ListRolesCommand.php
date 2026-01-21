<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use MIMAXUZ\LRoles\Models\XRoles;

class ListRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:list
                            {--with-permissions : Show permissions for each role}
                            {--with-users : Show user count for each role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $query = XRoles::query();

        if ($this->option('with-permissions')) {
            $query->with('permissions');
        }

        if ($this->option('with-users')) {
            $query->withCount('users');
        }

        $roles = $query->get();

        if ($roles->isEmpty()) {
            $this->warn('No roles found.');
            $this->line('Use "php artisan role:create" to create a new role.');
            return Command::SUCCESS;
        }

        if ($this->option('with-permissions')) {
            foreach ($roles as $role) {
                $this->info("Role: {$role->name} (slug: {$role->slug})");

                if ($this->option('with-users')) {
                    $this->line("  Users: {$role->users_count}");
                }

                $permissions = $role->permissions->pluck('slug')->toArray();
                $this->line('  Permissions: ' . (empty($permissions) ? 'None' : implode(', ', $permissions)));
                $this->newLine();
            }
        } else {
            $headers = ['ID', 'Name', 'Slug', 'Permissions'];

            if ($this->option('with-users')) {
                $headers[] = 'Users';
            }

            $rows = $roles->map(function ($role) {
                $row = [
                    $role->id,
                    $role->name,
                    $role->slug,
                    $role->permissions()->count(),
                ];

                if ($this->option('with-users')) {
                    $row[] = $role->users_count ?? $role->users()->count();
                }

                return $row;
            })->toArray();

            $this->table($headers, $rows);
        }

        return Command::SUCCESS;
    }
}
