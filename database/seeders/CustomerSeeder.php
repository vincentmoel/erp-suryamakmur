<?php

namespace Database\Seeders;

use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            // Retail / Individu
            [
                'type'         => CustomerType::INDIVIDUAL->value,
                'name'         => 'Budi Santoso',
                'company_name' => null,
                'tax_number'   => null,
                'email'        => 'budi.santoso@gmail.com',
                'phone'        => null,
                'mobile'       => '08111234567',
                'notes'        => null,
            ],
            [
                'type'         => CustomerType::INDIVIDUAL->value,
                'name'         => 'Siti Rahayu',
                'company_name' => null,
                'tax_number'   => null,
                'email'        => null,
                'phone'        => null,
                'mobile'       => '08129876543',
                'notes'        => null,
            ],

            // Warung / Rumah Makan
            [
                'type'         => CustomerType::INDIVIDUAL->value,
                'name'         => 'Ibu Dewi',
                'company_name' => 'Warung Makan Bu Dewi',
                'tax_number'   => null,
                'email'        => null,
                'phone'        => null,
                'mobile'       => '08135551234',
                'notes'        => 'Pelanggan tetap, order mingguan.',
            ],
            [
                'type'         => CustomerType::INDIVIDUAL->value,
                'name'         => 'Pak Hendra',
                'company_name' => 'RM Selera Nusantara',
                'tax_number'   => null,
                'email'        => null,
                'phone'        => '0217771234',
                'mobile'       => '08175552345',
                'notes'        => null,
            ],

            // Perusahaan / Distributor
            [
                'type'         => CustomerType::COMPANY->value,
                'name'         => 'Andi Wijaya',
                'company_name' => 'PT Maju Bersama Sejahtera',
                'tax_number'   => '01.234.567.8-901.000',
                'email'        => 'purchasing@majubersama.co.id',
                'phone'        => '0218889900',
                'mobile'       => '08161112233',
                'notes'        => 'Distributor area Jakarta & Tangerang.',
            ],
            [
                'type'         => CustomerType::COMPANY->value,
                'name'         => 'Rina Kusuma',
                'company_name' => 'CV Sumber Rezeki',
                'tax_number'   => '02.345.678.9-012.000',
                'email'        => 'order@sumberrezeki.com',
                'phone'        => null,
                'mobile'       => '08193334455',
                'notes'        => 'Distributor area Bandung & Cimahi.',
            ],
            [
                'type'         => CustomerType::COMPANY->value,
                'name'         => 'Doni Prasetyo',
                'company_name' => 'PT Dingin Segar Indonesia',
                'tax_number'   => '03.456.789.0-123.000',
                'email'        => 'procurement@dingin-segar.id',
                'phone'        => '0315556677',
                'mobile'       => '08215556677',
                'notes'        => 'Cold chain distributor, Surabaya & Jawa Timur.',
            ],

            // Supermarket / Minimarket
            [
                'type'         => CustomerType::COMPANY->value,
                'name'         => 'Agus Firmansyah',
                'company_name' => 'Toko Swalayan Harapan',
                'tax_number'   => null,
                'email'        => 'harapanswalayan@gmail.com',
                'phone'        => '0247778899',
                'mobile'       => null,
                'notes'        => 'Order bulanan, 2 cabang.',
            ],
            [
                'type'         => CustomerType::COMPANY->value,
                'name'         => 'Maya Indah',
                'company_name' => 'Minimarket Segar Jaya',
                'tax_number'   => null,
                'email'        => null,
                'phone'        => null,
                'mobile'       => '08576667788',
                'notes'        => null,
            ],

            // Katering
            [
                'type'         => CustomerType::COMPANY->value,
                'name'         => 'Farida Hanum',
                'company_name' => 'Katering Lezat Berkah',
                'tax_number'   => null,
                'email'        => 'katering.lezat@gmail.com',
                'phone'        => null,
                'mobile'       => '08229998877',
                'notes'        => 'Katering kantor, order harian.',
            ],
        ];

        foreach ($customers as $data) {
            Customer::firstOrCreate(
                ['name' => $data['name'], 'company_name' => $data['company_name']],
                $data
            );
        }
    }
}
