<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use MIMAXUZ\LRoles\Models\XRoles;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign
                            {user? : The user identifier (ID or email)}
                            {role? : The role slug to assign}
                            {--by=email : Lookup user by "id" or "email"}
                            {--remove : Remove the role instead of assigning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign or remove a role to/from a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $userModel = config('role-manager.user_model', 'App\\Models\\User');
        $lookupField = $this->option('by') ?: config('role-manager.user_lookup_field', 'email');

        // Get user identifier
        $userIdentifier = $this->argument('user');
        if (empty($userIdentifier)) {
            $userIdentifier = $this->ask("Enter the user {$lookupField}");
        }

        if (empty($userIdentifier)) {
            $this->error('User identifier is required.');
            return Command::FAILURE;
        }

        // Find user
        $user = $userModel::where($lookupField, $userIdentifier)->first();

        if (!$user) {
            $this->error("User not found with {$lookupField}: {$userIdentifier}");
            return Command::FAILURE;
        }

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

        // Get user display name
        $userDisplay = $user->email ?? $user->name ?? "ID: {$user->id}";

        // Assign or remove role
        if ($this->option('remove')) {
            $user->roles()->detach($role->id);
            $this->info("Role '{$role->name}' removed from user '{$userDisplay}'.");
        } else {
            if ($user->roles()->where('x_roles_id', $role->id)->exists()) {
                $this->warn("User '{$userDisplay}' already has the role '{$role->name}'.");
                return Command::SUCCESS;
            }

            $user->roles()->attach($role->id);
            $this->info("Role '{$role->name}' assigned to user '{$userDisplay}'.");
        }

        // Show current user roles
        $currentRoles = $user->roles()->pluck('name')->toArray();
        $this->line('Current roles: ' . (empty($currentRoles) ? 'None' : implode(', ', $currentRoles)));

        return Command::SUCCESS;
    }
}
