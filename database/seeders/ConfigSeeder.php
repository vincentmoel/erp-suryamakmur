<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $now    = now();
        $userId = DB::table('users')->orderBy('id')->value('id') ?? 1;

        $seeds = [
            // ── Company ──────────────────────────────────────────────
            ['key' => 'company_name',    'name' => 'Company Name',    'value' => config('app.name'), 'type' => 'text',     'section' => 'company'],
            ['key' => 'company_address', 'name' => 'Company Address', 'value' => '',                 'type' => 'textarea', 'section' => 'company'],
            ['key' => 'company_phone',   'name' => 'Company Phone',   'value' => '',                 'type' => 'text',     'section' => 'company'],
            ['key' => 'company_email',   'name' => 'Company Email',   'value' => '',                 'type' => 'text',     'section' => 'company'],
            ['key' => 'company_website', 'name' => 'Company Website', 'value' => '',                 'type' => 'text',     'section' => 'company'],
            ['key' => 'company_logo',    'name' => 'Company Logo',    'value' => '',                 'type' => 'image',    'section' => 'company'],

            // ── Bank ─────────────────────────────────────────────────
            ['key' => 'bank_name',           'name' => 'Bank Name',           'value' => '', 'type' => 'text', 'section' => 'bank'],
            ['key' => 'bank_account_number', 'name' => 'Bank Account Number', 'value' => '', 'type' => 'text', 'section' => 'bank'],
            ['key' => 'bank_account_holder', 'name' => 'Bank Account Holder', 'value' => '', 'type' => 'text', 'section' => 'bank'],

            // ── Invoice Numbering ─────────────────────────────────────
            ['key' => 'invoice_format',  'name' => 'Invoice Format',  'value' => 'INV-{Y}{m}-{seq}',  'type' => 'text',   'section' => 'invoice_numbering'],
            ['key' => 'invoice_padding', 'name' => 'Invoice Padding', 'value' => '4',                  'type' => 'number', 'section' => 'invoice_numbering'],

            // ── Bill Numbering ────────────────────────────────────────
            ['key' => 'bill_format',     'name' => 'Bill Format',     'value' => 'BILL-{Y}{m}-{seq}',  'type' => 'text',   'section' => 'bill_numbering'],
            ['key' => 'bill_padding',    'name' => 'Bill Padding',    'value' => '4',                  'type' => 'number', 'section' => 'bill_numbering'],
        ];

        foreach ($seeds as $seed) {
            DB::table('configs')->updateOrInsert(
                ['key' => $seed['key']],
                array_merge($seed, [
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
