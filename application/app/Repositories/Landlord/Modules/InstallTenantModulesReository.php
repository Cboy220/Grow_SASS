<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Spatie\Multitenancy\Models\Tenant;
use Module;

class TenantModulesRepository
{
    protected function getEnabledModules()
    {
        // Get all enabled modules from landlord database
        return DB::connection('landlord')
            ->table('modules')
            ->where('module_status', 'enabled')
            ->get();
    }

    protected function getMigrationFiles($module_name)
    {
        $migration_path = module_path($module_name, 'Database/Migrations');
        
        if (!File::isDirectory($migration_path)) {
            Log::info("No migration directory found", [
                'process' => 'modules.migration-check',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'module' => $module_name
            ]);
            return [];
        }

        return File::glob($migration_path . '/*.php');
    }

    protected function runModuleMigrations($module_name, $tenant)
    {
        try {
            // Run migrations for the specific module within tenant context
            $tenant->execute(function () use ($module_name) {
                Artisan::call('module:migrate', [
                    'module' => $module_name,
                    '--force' => true
                ]);
            });

            Log::info("Module migrations completed successfully", [
                'process' => 'modules.migration-execution',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'module' => $module_name,
                'database' => $tenant->database
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error running module migrations", [
                'process' => 'modules.migration-execution',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'module' => $module_name,
                'database' => $tenant->database,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    protected function synchronizeModulesTable($tenant)
    {
        try {
            // Get enabled modules from landlord
            $landlord_modules = $this->getEnabledModules();

            $tenant->execute(function () use ($landlord_modules) {
                // Create modules table in tenant if it doesn't exist
                if (!Schema::hasTable('modules')) {
                    Schema::create('modules', function ($table) {
                        $table->id();
                        $table->string('module_name');
                        $table->string('module_status');
                        $table->timestamps();
                    });
                }

                // Sync modules to tenant database
                foreach ($landlord_modules as $module) {
                    DB::table('modules')
                        ->updateOrInsert(
                            ['module_name' => $module->module_name],
                            [
                                'module_status' => 'enabled',
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                }
            });

            Log::info("Modules table synchronized successfully", [
                'process' => 'modules.table-sync',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'database' => $tenant->database
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error synchronizing modules table", [
                'process' => 'modules.table-sync',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'database' => $tenant->database,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function processTenantModules($database_name)
    {
        // Find the tenant by database name
        $tenant = Tenant::where('database', $database_name)->first();
        
        if (!$tenant) {
            Log::error("Tenant not found", [
                'process' => 'modules.processing',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'database' => $database_name
            ]);
            return false;
        }

        // Get all enabled modules
        $enabled_modules = $this->getEnabledModules();
        
        if ($enabled_modules->isEmpty()) {
            Log::info("No enabled modules found", [
                'process' => 'modules.processing',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__
            ]);
            return false;
        }

        // Process each module
        foreach ($enabled_modules as $module) {
            // Check for migrations
            $migration_files = $this->getMigrationFiles($module->module_name);
            
            if (!empty($migration_files)) {
                // Run migrations for this module
                $this->runModuleMigrations($module->module_name, $tenant);
            }
        }

        // Synchronize modules table
        return $this->synchronizeModulesTable($tenant);
    }
}