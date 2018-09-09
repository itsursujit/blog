<?php namespace Modules\Gateway\Services;
use Modules\Gateway\Contracts\ServiceRegistryContract;

/**
 * File DNSRegistry
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Services
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
class DNSRegistry implements ServiceRegistryContract
{

    /**
     * Find an instance of a specified microservice
     * Returns URL (RESTful services always have URLs)
     *
     * @param $serviceId
     *
     * @return string
     */
    public function resolveInstance($serviceId)
    {
        $config = config('gateway');

        // If service doesn't have a specific URL, simply append global domain to service name
        $hostname = $config['services'][$serviceId]['hostname'] ?? $serviceId . '.' . $config['global']['domain'];

        return 'http://' .  $hostname;
    }

}