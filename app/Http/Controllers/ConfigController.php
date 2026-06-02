<?php

namespace App\Http\Controllers;

use App\DataTables\BaseDataTable;
use App\DataTables\ConfigDataTable;
use App\Enums\Module;
use App\Http\Requests\ConfigRequest;
use App\Models\Config;

class ConfigController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Config::class,
            'configs',
            'Config',
            'configs',
            Module::Config->name,
            ConfigRequest::class,
            ConfigDataTable::class,
            ['show', 'edit', 'delete']
        );
    }

    public static function getDiscountPercentage()
    {
        $config = Config::where('key', 'member_discount')->firstOrFail();

        return $config->value;
    }
}