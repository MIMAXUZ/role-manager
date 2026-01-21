<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use MIMAXUZ\LRoles\Models\XRoles;

class CreateRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create
                            {name? : The name of the role}
                            {--slug= : The slug for the role (auto-generated if not provided)}
                            {--force : Update if role with same slug exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new role';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        if (empty($name)) {
            $name = $this->ask('Enter the role name');
        }

        if (empty($name)) {
            $this->error('Role name is required.');
            return Command::FAILURE;
        }

        $slug = $this->option('slug') ?: Str::slug($name);

        $existingRole = XRoles::where('slug', $slug)->first();

        if ($existingRole && !$this->option('force')) {
            $this->error("Role with slug '{$slug}' already exists.");
            $this->line("Use --force to update the existing role.");
            return Command::FAILURE;
        }

        $role = XRoles::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'slug' => $slug]
        );

        if ($existingRole) {
            $this->info("Role '{$name}' (slug: {$slug}) updated successfully.");
        } else {
            $this->info("Role '{$name}' (slug: {$slug}) created successfully.");
        }

        return Command::SUCCESS;
    }
}
