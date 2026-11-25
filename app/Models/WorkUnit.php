<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkUnit extends Model
{
    const TYPE_KANTOR = 'kantor';
    const TYPE_SUB_BAGIAN = 'sub_bagian';
    const TYPE_SEKSI = 'seksi';
    const TYPE_PENYELENGGARA = 'penyelenggara';
    const TYPE_KELOMPOK_FUNGSIONAL = 'kelompok_fungsional';

    protected $fillable = [
        'parent_id',
        'name',
        'short_name',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(WorkUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WorkUnit::class, 'parent_id')->orderBy('sort_order');
    }

    /** Semua penempatan (kepala + staf) di unit ini. */
    public function positions()
    {
        return $this->hasMany(EmployeePosition::class)->orderBy('sort_order');
    }

    /** Penempatan kepala unit (termasuk Plt). */
    public function headPosition()
    {
        return $this->hasOne(EmployeePosition::class)->where('is_head', true);
    }

    /** Penempatan staf (bukan kepala). */
    public function staffPositions()
    {
        return $this->hasMany(EmployeePosition::class)
            ->where('is_head', false)
            ->orderBy('sort_order');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_positions')
            ->withPivot(['title', 'is_head', 'is_acting', 'sort_order'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /** Sub bagian digambar sebagai cabang samping pada bagan. */
    public function isSideBranch()
    {
        return $this->type === self::TYPE_SUB_BAGIAN;
    }

    /** Kelompok jabatan fungsional: tidak punya kepala, tidak digambar di bagan. */
    public function isFunctionalGroup()
    {
        return $this->type === self::TYPE_KELOMPOK_FUNGSIONAL;
    }

    /** Unit yang digambar sebagai cabang utama (seksi/penyelenggara) pada bagan. */
    public function isChartBranch()
    {
        return !$this->isSideBranch() && !$this->isFunctionalGroup();
    }

    public static function typeLabels()
    {
        return [
            self::TYPE_KANTOR => 'Kantor',
            self::TYPE_SUB_BAGIAN => 'Sub Bagian',
            self::TYPE_SEKSI => 'Seksi',
            self::TYPE_PENYELENGGARA => 'Penyelenggara',
            self::TYPE_KELOMPOK_FUNGSIONAL => 'Kelompok Jabatan Fungsional',
        ];
    }

    public function getTypeLabelAttribute()
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    /**
     * Semua unit dalam urutan pohon (induk lalu anak-anaknya),
     * masing-masing diberi atribut sementara `depth`.
     */
    public static function flatTree($units = null)
    {
        $units = $units ?: static::query()->orderBy('sort_order')->orderBy('name')->get();
        $byParent = $units->groupBy(function ($unit) {
            return $unit->parent_id ?: 0;
        });

        $result = collect();
        $walk = function ($parentId, $depth) use (&$walk, $byParent, $result) {
            foreach ($byParent->get($parentId, []) as $unit) {
                $unit->depth = $depth;
                $result->push($unit);
                $walk($unit->id, $depth + 1);
            }
        };
        $walk(0, 0);

        return $result;
    }

    /** ID seluruh turunan unit ini (anak, cucu, dst). */
    public function descendantIds()
    {
        $byParent = static::query()->get(['id', 'parent_id'])->groupBy('parent_id');

        $ids = [];
        $stack = [$this->id];
        while ($stack) {
            $id = array_pop($stack);
            foreach ($byParent->get($id, []) as $child) {
                $ids[] = $child->id;
                $stack[] = $child->id;
            }
        }

        return $ids;
    }
}
