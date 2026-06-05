<?php

namespace App\Http\Controllers;

use App\DataTables\CustomerDataTable;
use App\Enums\CustomerType;
use App\Enums\Module;
use App\Http\Controllers\Traits\IsActive;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends BaseController
{
    use IsActive;
    public function __construct()
    {
        parent::__construct(
            Customer::class,
            'customers',
            'Customer',
            'customers',
            Module::Customer->name,
            CustomerRequest::class,
            CustomerDataTable::class,
        );
    }

    public function create()
    {
        return view('customers.create', [
            'title'         => $this->title,
            'route'         => $this->route,
            'customerTypes' => CustomerType::cases(),
        ]);
    }

    public function edit($encryptedId)
    {
        $data = Customer::findOrFail(\App\Helpers\Encryption::decrypt($encryptedId));

        return view('customers.edit', [
            'data'          => $data,
            'title'         => $this->title,
            'route'         => $this->route,
            'encryptedId'   => $encryptedId,
            'customerTypes' => CustomerType::cases(),
        ]);
    }
}
