<?php namespace Modules\Gateway\Services;
use Modules\Gateway\Contracts\ServiceRegistryContract;

/**
 * File RandonDNSRegistry
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
class RandomDNSRegistry implements ServiceRegistryContract
{
    /**
     * @param string $serviceId
     * @return string
     */
    public function resolveInstance($serviceId)
    {
        $config = config('gateway');

        $hosts = $config['services'][$serviceId]['hostname'];

        if (is_array($hosts) && $hosts) {
            $hostname = $hosts[array_rand($hosts)];
        } else {
            $hostname = $hosts ?? "{$serviceId}.{$config['global']['domain']}";
        }

        return "http://{$hostname}";
    }

}