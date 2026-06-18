<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Helpers\CodeGenerator;
use App\Helpers\FileManager;
use App\Helpers\Response;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    private string $module = Module::Config->name;

    public function index()
    {
        $this->checkPerm('read');

        $sections = [
            'company'           => Config::section('company'),
            'bank'              => Config::section('bank'),
            'invoice_numbering' => Config::section('invoice_numbering'),
            'bill_numbering'    => Config::section('bill_numbering'),
        ];

        return view('configs.index', [
            'title'    => 'Pengaturan',
            'sections' => $sections,
        ]);
    }

    public function ajaxSave(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->checkPerm('update');

        $section = $request->input('section');

        try {
            match ($section) {
                'company'           => $this->saveCompany($request),
                'bank'              => $this->saveBank($request),
                'invoice_numbering' => $this->saveNumbering($request, 'invoice'),
                'bill_numbering'    => $this->saveNumbering($request, 'bill'),
                default             => throw new \InvalidArgumentException('Section tidak dikenali.'),
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Response::withErrors(422, 'Validasi gagal.', $e->errors());
        } catch (\InvalidArgumentException $e) {
            return Response::build(400, $e->getMessage());
        }

        return Response::build(200, 'Pengaturan berhasil disimpan.');
    }

    public function previewCode(Request $request): \Illuminate\Http\JsonResponse
    {
        $preview = CodeGenerator::preview(
            template: $request->input('format', '{seq}'),
            padding:  (int) $request->input('padding', 4),
        );

        return response()->json(['preview' => $preview]);
    }

    public static function getDiscountPercentage(): string
    {
        return Config::get('member_discount', '0');
    }

    private function saveCompany(Request $request): void
    {
        $fields = ['company_name', 'company_address', 'company_phone', 'company_email', 'company_website'];

        foreach ($fields as $field) {
            Config::set($field, $request->input($field, ''));
        }

        if ($request->hasFile('company_logo')) {
            $request->validate(['company_logo' => 'image|max:2048']);
            $path = FileManager::store($request->file('company_logo'), 'logos');
            Config::set('company_logo', $path);
        }
    }

    private function saveBank(Request $request): void
    {
        foreach (['bank_name', 'bank_account_number', 'bank_account_holder'] as $field) {
            Config::set($field, $request->input($field, ''));
        }
    }

    private function saveNumbering(Request $request, string $doc): void
    {
        $request->validate([
            "{$doc}_format"  => ['required', 'string', 'max:100', 'regex:/\{seq\}/'],
            "{$doc}_padding" => 'required|integer|min:1|max:10',
        ], [
            "{$doc}_format.regex" => 'Format harus mengandung token {seq}.',
        ]);

        Config::set("{$doc}_format",  $request->input("{$doc}_format"));
        Config::set("{$doc}_padding", $request->input("{$doc}_padding"));
    }

    private function checkPerm(string $action): void
    {
        $perms = app(\App\Services\PermissionService::class);
        abort_unless($perms->has($this->module, $action), 403);
    }
}
