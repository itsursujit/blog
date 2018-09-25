<?php namespace Modules\Gateway\Entities;
use Illuminate\Database\Eloquent\Model;

/**
 * File Service
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Entities
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 intergo.com.cy. All rights reserved.
 */
class Service extends Model
{
    protected $table = 'services_modules';

    public function servers()
    {
        return $this->hasMany(ProxyServer::class);
    }
}