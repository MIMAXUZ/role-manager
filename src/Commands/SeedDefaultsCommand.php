<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MIMAXUZ\LRoles\Models\XPermissions;
use MIMAXUZ\LRoles\Models\XRoles;

class SeedDefaultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:seed
                            {--fresh : Clear existing roles and permissions before seeding}
                            {--roles-only : Only seed roles (not permissions)}
                            {--permissions-only : Only seed permissions (not roles)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default roles and permissions from config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('fresh')) {
            if (!$this->confirm('This will delete all existing roles, permissions, and their relationships. Continue?', false)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->freshDatabase();
        }

        $seededPermissions = [];
        $seededRoles = [];

        // Seed permissions first (they need to exist before attaching to roles)
        if (!$this->option('roles-only')) {
            $seededPermissions = $this->seedPermissions();
        }

        // Seed roles
        if (!$this->option('permissions-only')) {
            $seededRoles = $this->seedRoles();
        }

        $this->newLine();
        $this->info('Seeding completed!');
        $this->table(
            ['Type', 'Count'],
            [
                ['Permissions', count($seededPermissions)],
                ['Roles', count($seededRoles)],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Clear existing data.
     *
     * @return void
     */
    private function freshDatabase(): void
    {
        $this->warn('Clearing existing data...');

        DB::table(config('role-manager.tables.role_permissions', 'roles_permissions'))->truncate();
        DB::table(config('role-manager.tables.user_roles', 'users_roles'))->truncate();
        XRoles::truncate();
        XPermissions::truncate();

        $this->info('Existing data cleared.');
    }

    /**
     * Seed permissions from config.
     *
     * @return array
     */
    private function seedPermissions(): array
    {
        $permissions = config('role-manager.default_permissions', []);
        $seeded = [];

        if (empty($permissions)) {
            $this->warn('No default permissions defined in config.');
            return $seeded;
        }

        $this->info('Seeding permissions...');
        $bar = $this->output->createProgressBar(count($permissions));
        $bar->start();

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $slug = $permission;
                $name = $this->slugToName($permission);
            } else {
                $slug = $permission['slug'];
                $name = $permission['name'] ?? $this->slugToName($slug);
            }

            XPermissions::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );

            $seeded[] = $slug;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $seeded;
    }

    /**
     * Seed roles from config.
     *
     * @return array
     */
    private function seedRoles(): array
    {
        $roles = config('role-manager.default_roles', []);
        $seeded = [];

        if (empty($roles)) {
            $this->warn('No default roles defined in config.');
            return $seeded;
        }

        $this->info('Seeding roles...');
        $bar = $this->output->createProgressBar(count($roles));
        $bar->start();

        foreach ($roles as $roleData) {
            $role = XRoles::firstOrCreate(
                ['slug' => $roleData['slug']],
                ['name' => $roleData['name']]
            );

            // Attach permissions if specified
            if (isset($roleData['permissions']) && !empty($roleData['permissions'])) {
                $permissionSlugs = $roleData['permissions'];

                // Handle wildcard '*' for all permissions
                if (in_array('*', $permissionSlugs, true)) {
                    $permissionIds = XPermissions::pluck('id')->toArray();
                } else {
                    $permissionIds = XPermissions::whereIn('slug', $permissionSlugs)
                        ->pluck('id')
                        ->toArray();
                }

                if (!empty($permissionIds)) {
                    $role->permissions()->syncWithoutDetaching($permissionIds);
                }
            }

            $seeded[] = $role->slug;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $seeded;
    }

    /**
     * Convert slug to human readable name.
     *
     * @param string $slug
     * @return string
     */
    private function slugToName(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
