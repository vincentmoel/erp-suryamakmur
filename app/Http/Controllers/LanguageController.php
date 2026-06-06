<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function update(string $lang)
    {
        abort_if(!in_array($lang, ['id', 'en']), 404);

        auth()->user()->update(['language' => $lang]);

        return response()->json(['success' => true]);
    }
}
