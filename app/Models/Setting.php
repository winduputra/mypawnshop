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

    /**
     * Get biaya admin flat per kategori barang.
     */
    public static function getBiayaAdmin(string $kategori): float
    {
        $key = 'biaya_admin_' . $kategori;
        return (float) static::getValue($key, 35000);
    }

    /**
     * Get ijarah percentage (% dari taksiran per 30 hari).
     */
    public static function getIjarahPersen(): float
    {
        return (float) static::getValue('ijarah_persen', 2);
    }
}
