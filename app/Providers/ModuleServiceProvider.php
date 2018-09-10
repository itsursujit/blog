<?php

namespace App\Providers;

use App\Model\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $modulesPath = base_path() . '/Modules';
        $folders = glob($modulesPath . '/*');
        $modules = DB::table('services_modules')->where('status', 1)->pluck('providers', 'name');
        foreach($folders as $folder)
        {
            foreach ($modules as $module => $providers)
            {
                if(basename($folder) === $module)
                {
                    $providers = json_decode($providers, true);
                    foreach($providers as $provider)
                    {
                        $this->app->register($provider);
                    }
                    require $folder . '/start.php';
                }
            }
        }
    }

    public function registerProviders()
    {

    }
}
