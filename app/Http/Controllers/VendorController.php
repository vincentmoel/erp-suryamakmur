<?php

namespace App\Http\Controllers;

use App\DataTables\VendorDataTable;
use App\Enums\Module;
use App\Enums\VendorType;
use App\Helpers\CodeGenerator;
use App\Helpers\Encryption;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;

class VendorController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Vendor::class,
            'vendors',
            'Vendor',
            'vendors',
            Module::Vendor->name,
            VendorRequest::class,
            VendorDataTable::class,
        );
    }

    public function create()
    {
        return view('vendors.create', [
            'title'       => $this->title,
            'route'       => $this->route,
            'vendorTypes' => VendorType::cases(),
            'autoCode'    => CodeGenerator::vendor(),
        ]);
    }

    public function edit($encryptedId)
    {
        $data = Vendor::findOrFail(Encryption::decrypt($encryptedId));

        return view('vendors.edit', [
            'data'        => $data,
            'title'       => $this->title,
            'route'       => $this->route,
            'encryptedId' => $encryptedId,
            'vendorTypes' => VendorType::cases(),
        ]);
    }
}
