<?php namespace Modules\Gateway\Contracts;
/**
 * File ServiceRegisterContract
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Contracts
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 sms.to. All rights reserved.
 */
interface ServiceRegistryContract
{
    /**
    * Find an instance of a specified microservice
    * Returns URL (RESTful services always have URLs)
    *
    * @param $serviceId
    * @return string
    */
    public function resolveInstance($serviceId);

}