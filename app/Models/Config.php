<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public static function get(string $key, string $default = ''): string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::where('key', $key)->update([
            'value'      => $value ?? '',
            'updated_by' => auth()->id(),
        ]);
    }

    public static function section(string $section): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('section', $section)->get()->keyBy('key');
    }
}
