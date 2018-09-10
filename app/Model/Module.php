<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'services_modules';

    protected $fillable = [
        'name',
        'alias',
        'type',
        'providers',
        'proxies',
        'routes',
        'status',
    ];
}
