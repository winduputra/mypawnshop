<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'pengaturan';
    protected $fillable = ['key', 'value', 'label', 'group'];

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    public static function getLoanPercentage(string $kategori): float
    {
        $key = 'persentase_' . $kategori;
        return (float) static::getValue($key, 75) / 100;
    }

    /**
     * Get ujrah (biaya penitipan) per 30 days for a category (flat Rp).
     */
    public static function getUjrah(string $kategori): float
    {
        $key = 'ujrah_' . $kategori;
        return (float) static::getValue($key, 50000);
    }
}
