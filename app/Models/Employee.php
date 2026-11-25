<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    const TYPE_PNS = 'pns';
    const TYPE_PPPK = 'pppk';
    const TYPE_NON_ASN = 'non_asn';

    protected $fillable = [
        'name',
        'nip',
        'photo',
        'employment_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function positions()
    {
        return $this->hasMany(EmployeePosition::class);
    }

    public function workUnits()
    {
        return $this->belongsToMany(WorkUnit::class, 'employee_positions')
            ->withPivot(['title', 'is_head', 'is_acting', 'sort_order'])
            ->withTimestamps();
    }

    /** Akun login (hanya sebagian kecil pegawai yang punya). */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kunci urutan senioritas dari NIP:
     * digit 9–12 = tahun masuk (TMT CPNS/PPPK), lalu digit 1–8 = tanggal lahir (lebih tua lebih dulu).
     * Pegawai tanpa NIP (Non-ASN) ditaruh paling akhir.
     */
    public function getSeniorityKeyAttribute()
    {
        if (!preg_match('/^\d{18}$/', (string) $this->nip)) {
            return '999999999999';
        }

        return substr($this->nip, 8, 4) . substr($this->nip, 0, 8);
    }

    /**
     * Inisial untuk avatar pengganti foto, tanpa gelar.
     * "H. Yufrizal, S.Ag., M.H.I." => "Y", "M. Fatih Alfaris" => "MF".
     */
    public function getInitialsAttribute()
    {
        $name = trim(explode(',', $this->name)[0]);
        $name = preg_replace('/^(H|Hj|Dr|Drs|Dra|Ir)\.\s+/i', '', $name);

        $words = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials;
    }

    public static function typeLabels()
    {
        return [
            self::TYPE_PNS => 'PNS',
            self::TYPE_PPPK => 'PPPK',
            self::TYPE_NON_ASN => 'Non-ASN',
        ];
    }

    public function getEmploymentTypeLabelAttribute()
    {
        return self::typeLabels()[$this->employment_type] ?? $this->employment_type;
    }

    /**
     * URL foto, atau null jika belum ada foto.
     * Memakai asset() agar tetap benar walau aplikasi berjalan di sub-folder.
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
