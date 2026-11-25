<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Penempatan pegawai di unit kerja.
 *
 * Satu penempatan bisa berlaku di versi DUK (definitif/SK), versi penugasan
 * (nota tugas/surat perintah, termasuk Plt), atau keduanya.
 */
class EmployeePosition extends Model
{
    /** Versi halaman struktur organisasi. */
    const VERSION_DUK = 'duk';
    const VERSION_ASSIGNMENT = 'penugasan';

    /** Cakupan penempatan (dipakai di form admin), dipetakan ke in_duk & in_assignment. */
    const SCOPE_BOTH = 'semua';
    const SCOPE_DUK_ONLY = 'duk';
    const SCOPE_ASSIGNMENT_ONLY = 'tugas';

    protected $fillable = [
        'employee_id',
        'work_unit_id',
        'title',
        'is_head',
        'is_acting',
        'in_duk',
        'in_assignment',
        'sort_order',
    ];

    protected $casts = [
        'is_head' => 'boolean',
        'is_acting' => 'boolean',
        'in_duk' => 'boolean',
        'in_assignment' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public static function versionLabels()
    {
        return [
            self::VERSION_ASSIGNMENT => 'Penugasan (Nota Tugas)',
            self::VERSION_DUK => 'Sesuai DUK',
        ];
    }

    public static function scopeLabels()
    {
        return [
            self::SCOPE_BOTH => 'DUK & penugasan',
            self::SCOPE_DUK_ONLY => 'Hanya DUK (ditugaskan di unit lain)',
            self::SCOPE_ASSIGNMENT_ONLY => 'Hanya penugasan (nota tugas / Plt)',
        ];
    }

    /** Kolom flag untuk satu versi. */
    public static function versionColumn($version)
    {
        return $version === self::VERSION_DUK ? 'in_duk' : 'in_assignment';
    }

    /** Ubah pilihan cakupan di form menjadi nilai kolom. */
    public static function scopeToFlags($scope)
    {
        return [
            'in_duk' => $scope !== self::SCOPE_ASSIGNMENT_ONLY,
            'in_assignment' => $scope !== self::SCOPE_DUK_ONLY,
        ];
    }

    /** Batasi query ke penempatan yang berlaku pada satu versi. */
    public function scopeForVersion($query, $version)
    {
        return $query->where(self::versionColumn($version), true);
    }

    /** Cakupan penempatan ini untuk form admin. */
    public function getScopeAttribute()
    {
        if ($this->in_duk && !$this->in_assignment) {
            return self::SCOPE_DUK_ONLY;
        }
        if (!$this->in_duk && $this->in_assignment) {
            return self::SCOPE_ASSIGNMENT_ONLY;
        }

        return self::SCOPE_BOTH;
    }

    public function getScopeLabelAttribute()
    {
        return self::scopeLabels()[$this->scope];
    }

    /**
     * Keterangan singkat untuk staf yang penempatannya berbeda antara DUK dan penugasan.
     * Membutuhkan relasi employee.positions.workUnit sudah dimuat.
     *
     * Versi penugasan: "Nota tugas · DUK: Sub Bagian Tata Usaha"
     * Versi DUK:       "Ditugaskan ke Seksi Pendidikan Agama Islam"
     */
    public function versionNote($version)
    {
        if ($this->is_head) {
            return null; // Plt sudah tertulis di jabatan
        }

        $others = $this->employee->positions->where('id', '!=', $this->id);

        if ($version === self::VERSION_ASSIGNMENT && !$this->in_duk) {
            $home = $others->first(function ($p) {
                return $p->in_duk && !$p->is_head;
            });

            return 'Nota tugas' . ($home && $home->workUnit ? ' · DUK: ' . $home->workUnit->name : '');
        }

        if ($version === self::VERSION_DUK && !$this->in_assignment) {
            $target = $others->first(function ($p) {
                return $p->in_assignment && !$p->is_head;
            });

            return $target && $target->workUnit ? 'Ditugaskan ke ' . $target->workUnit->name : 'Sedang ditugaskan di unit lain';
        }

        return null;
    }

    /**
     * Urutkan penempatan berdasarkan senioritas pegawai
     * (tahun masuk dari NIP, lalu tanggal lahir, lalu nama).
     */
    public static function sortBySeniority($positions)
    {
        return $positions->sortBy(function ($position) {
            return $position->employee->seniority_key . '|' . mb_strtolower($position->employee->name);
        })->values();
    }
}
