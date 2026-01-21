<?php

namespace MIMAXUZ\LRoles\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use MIMAXUZ\LRoles\Models\XPermissions;

class CreatePermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create
                            {name? : The name of the permission}
                            {--slug= : The slug for the permission (auto-generated if not provided)}
                            {--force : Update if permission with same slug exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new permission';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        if (empty($name)) {
            $name = $this->ask('Enter the permission name');
        }

        if (empty($name)) {
            $this->error('Permission name is required.');
            return Command::FAILURE;
        }

        $slug = $this->option('slug') ?: Str::slug($name);

        $existingPermission = XPermissions::where('slug', $slug)->first();

        if ($existingPermission && !$this->option('force')) {
            $this->error("Permission with slug '{$slug}' already exists.");
            $this->line("Use --force to update the existing permission.");
            return Command::FAILURE;
        }

        $permission = XPermissions::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'slug' => $slug]
        );

        if ($existingPermission) {
            $this->info("Permission '{$name}' (slug: {$slug}) updated successfully.");
        } else {
            $this->info("Permission '{$name}' (slug: {$slug}) created successfully.");
        }

        return Command::SUCCESS;
    }
}
