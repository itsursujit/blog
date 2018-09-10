<?php

namespace App\Console\Commands;

use App\Model\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateServiceRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:route-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $modules = Module::all()->toArray();
        $gatewayRoutes = [];
        $authRoutes = [];
        $serviceRoutes = [];
        foreach($modules as $key => $module)
        {
            if($module['type'] === 'gateway')
            {
                $gateway = $module;
                $gateway['providers'] = json_decode($module['providers'], true);
                $gateway['proxies'] = json_decode($module['proxies'], true);
                $gateway['routes'] = json_decode($module['routes'], true);
                Storage::disk('local')->put('gateway-routes.json', json_encode($gateway, JSON_PRETTY_PRINT));
            }
            elseif($module['type'] === 'authentication')
            {
                $auth = $module;
                $auth['providers'] = json_decode($module['providers'], true);
                $auth['proxies'] = json_decode($module['proxies'], true);
                $auth['routes'] = json_decode($module['routes'], true);
                Storage::disk('local')->put('auth-routes.json', json_encode($auth, JSON_PRETTY_PRINT));
            }
            else
            {
                $service = $module;
                $service['providers'] = json_decode($module['providers'], true);
                $service['proxies'] = json_decode($module['proxies'], true);
                $service['routes'] = json_decode($module['routes'], true);
                $serviceRoutes[] = $service;
            }
            Storage::disk('local')->put('service-routes.json', json_encode($serviceRoutes, JSON_PRETTY_PRINT));
        }

    }
}
