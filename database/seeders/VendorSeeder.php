<?php

namespace Database\Seeders;

use App\Enums\VendorType;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            // Supplier Lokal
            [
                'type'                => VendorType::COMPANY->value,
                'name'                => 'PT Sumber Makmur Pangan',
                'tax_number'          => '01.987.654.3-210.000',
                'phone'               => '0215551001',
                'email'               => 'order@sumbermakmur.co.id',
                'contact_person'      => 'Bapak Hendra',
                'address'             => 'Jl. Industri Raya No. 15',
                'city'                => 'Jakarta',
                'province'            => 'DKI Jakarta',
                'postal_code'         => '14440',
                'bank_name'           => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name'   => 'PT Sumber Makmur Pangan',
                'notes'               => 'Supplier utama nugget dan sosis.',
                'is_active'           => true,
            ],
            [
                'type'                => VendorType::COMPANY->value,
                'name'                => 'CV Dingin Abadi',
                'tax_number'          => null,
                'phone'               => '0317772002',
                'email'               => 'sales@dinginabadi.com',
                'contact_person'      => 'Ibu Ratna',
                'address'             => 'Jl. Rungkut Industri III No. 8',
                'city'                => 'Surabaya',
                'province'            => 'Jawa Timur',
                'postal_code'         => '60293',
                'bank_name'           => 'Mandiri',
                'bank_account_number' => '0987654321',
                'bank_account_name'   => 'CV Dingin Abadi',
                'notes'               => 'Supplier seafood beku dan daging.',
                'is_active'           => true,
            ],
            [
                'type'                => VendorType::COMPANY->value,
                'name'                => 'UD Mitra Sejahtera',
                'tax_number'          => null,
                'phone'               => null,
                'email'               => null,
                'contact_person'      => 'Pak Joko',
                'address'             => 'Jl. Pasar Baru No. 22',
                'city'                => 'Bandung',
                'province'            => 'Jawa Barat',
                'postal_code'         => '40111',
                'bank_name'           => null,
                'bank_account_number' => null,
                'bank_account_name'   => null,
                'notes'               => 'Supplier kulit lumpia dan pastry.',
                'is_active'           => true,
            ],

            // Supplier Impor
            [
                'type'                => VendorType::COMPANY->value,
                'name'                => 'Golden Ocean Foods Co., Ltd.',
                'tax_number'          => null,
                'phone'               => '+862112345678',
                'email'               => 'export@goldenocean.cn',
                'contact_person'      => 'Mr. Wei',
                'address'             => '88 Seafood Industry Road, Qingdao',
                'city'                => 'Qingdao',
                'province'            => 'Shandong',
                'postal_code'         => null,
                'bank_name'           => null,
                'bank_account_number' => null,
                'bank_account_name'   => null,
                'notes'               => 'Supplier dimsum dan produk seafood impor.',
                'is_active'           => true,
            ],
        ];

        foreach ($vendors as $data) {
            Vendor::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
