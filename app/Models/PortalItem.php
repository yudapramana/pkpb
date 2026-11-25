<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalItem extends Model
{
    use HasFactory;

    /** Ikon bawaan bila item belum punya gambar. */
    const DEFAULT_IMAGE = 'http://res.cloudinary.com/kemenagpessel/image/upload/v1727767964/profile_picture_pegawai/adioqte79tz0rfjhcx56.png';

    protected $fillable = [
        'title',
        'image_url',
        'image_path',
        'destination_url',
        'is_modal',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_modal' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Sumber gambar yang ditampilkan: unggahan > URL eksternal > ikon bawaan. */
    public function getImageSrcAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        if (!$this->image_url) {
            return self::DEFAULT_IMAGE;
        }

        // Path lokal (mis. /images/portal/simpeg.svg) dari folder public.
        if (strpos($this->image_url, '/') === 0 && strpos($this->image_url, '//') !== 0) {
            return asset(ltrim($this->image_url, '/'));
        }

        return $this->image_url;
    }

    public function getHasLinkAttribute()
    {
        return !empty($this->destination_url);
    }
}
