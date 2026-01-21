<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use MIMAXUZ\LRoles\Models\XPermissions;
use MIMAXUZ\LRoles\Models\XRoles;

class AttachPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:permission
                            {role? : The role slug}
                            {permissions?* : The permission slug(s) to attach}
                            {--detach : Detach the permission(s) instead of attaching}
                            {--sync : Sync permissions (replace all existing with new)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach or detach permission(s) to/from a role';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Get role
        $roleSlug = $this->argument('role');
        if (empty($roleSlug)) {
            $roles = XRoles::pluck('name', 'slug')->toArray();

            if (empty($roles)) {
                $this->error('No roles found. Create roles first using role:create');
                return Command::FAILURE;
            }

            $roleSlug = $this->choice('Select a role', array_keys($roles));
        }

        $role = XRoles::where('slug', $roleSlug)->first();

        if (!$role) {
            $this->error("Role '{$roleSlug}' not found.");
            return Command::FAILURE;
        }

        // Get permissions
        $permissionSlugs = $this->argument('permissions');
        if (empty($permissionSlugs)) {
            $permissions = XPermissions::pluck('name', 'slug')->toArray();

            if (empty($permissions)) {
                $this->error('No permissions found. Create permissions first using permission:create');
                return Command::FAILURE;
            }

            $this->info('Available permissions:');
            foreach ($permissions as $slug => $name) {
                $this->line("  - {$slug} ({$name})");
            }

            $selectedSlugs = $this->ask('Enter permission slug(s) separated by comma');
            $permissionSlugs = array_map('trim', explode(',', $selectedSlugs));
        }

        // Validate permissions
        $permissionIds = XPermissions::whereIn('slug', $permissionSlugs)->pluck('id', 'slug');

        $invalidSlugs = array_diff($permissionSlugs, $permissionIds->keys()->toArray());
        if (!empty($invalidSlugs)) {
            $this->warn('Invalid permission slug(s): ' . implode(', ', $invalidSlugs));
        }

        if ($permissionIds->isEmpty()) {
            $this->error('No valid permissions found.');
            return Command::FAILURE;
        }

        // Perform action
        if ($this->option('sync')) {
            $role->permissions()->sync($permissionIds->values()->toArray());
            $this->info("Permissions synced to role '{$role->name}'.");
        } elseif ($this->option('detach')) {
            $role->permissions()->detach($permissionIds->values()->toArray());
            $this->info("Permissions detached from role '{$role->name}'.");
        } else {
            $role->permissions()->syncWithoutDetaching($permissionIds->values()->toArray());
            $this->info("Permissions attached to role '{$role->name}'.");
        }

        // Show current permissions
        $this->newLine();
        $this->info("Current permissions for role '{$role->name}':");

        $currentPermissions = $role->permissions()->get(['name', 'slug'])->toArray();

        if (empty($currentPermissions)) {
            $this->line('  No permissions assigned.');
        } else {
            $this->table(['Name', 'Slug'], $currentPermissions);
        }

        return Command::SUCCESS;
    }
}
