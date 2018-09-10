<?php

namespace App\Console\Commands;

use App\Model\Module;
use Illuminate\Console\Command;
use \Illuminate\Support\Facades\Route as OrigRoute;

class RegisterModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers all modules inside Modules folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modulesPath = base_path() . '/Modules';
        $folders = glob($modulesPath . '/*');
        foreach($folders as $folder)
        {
            $moduleJson = json_decode(file_get_contents($folder . '/module.json'), true);
            $moduleRoutes = $this->getModuleRoutes($moduleJson['name']);
            $routes = [];
            foreach($moduleRoutes as $route)
            {
                $routes[] = [
                    'uri' => $route->uri,
                    'method' => $route->methods[0],
                    'parameters' => json_encode($route->parameterNames),
                ];
            }
            Module::create([
               'name' => $moduleJson['name'],
               'alias' => $moduleJson['alias'],
               'type' => $moduleJson['type'],
               'providers' => json_encode($moduleJson['providers']),
               'routes' => json_encode($routes),
               'proxies' => json_encode($moduleJson['proxies']),
               'status' => $moduleJson['active']
            ]);
        }
    }



    function getModuleRoutes($moduleName, $baseModulesPath = 'Modules')
    {
        return collect(OrigRoute::getRoutes())->filter(function ($item) use ($baseModulesPath, $moduleName) {
            return isset($item->action['namespace']) && starts_with($item->action['namespace'], "{$baseModulesPath}\\{$moduleName}");
        })->values()->all();
    }
}
