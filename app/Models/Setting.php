<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type'];
    public $timestamps = false;

    public static function get($key, $default = null)
    {
        return static::where('key', $key)->first()?->value ?? $default;
    }

    public static function set($key, $value, $type = 'string')
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
    }
}
