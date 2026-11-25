<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkUnitRequest;
use App\Models\WorkUnit;

class WorkUnitController extends Controller
{
    public function index()
    {
        $units = WorkUnit::flatTree(
            WorkUnit::query()
                ->withCount([
                    'staffPositions as staff_duk_count' => function ($q) {
                        $q->where('in_duk', true);
                    },
                    'staffPositions as staff_assignment_count' => function ($q) {
                        $q->where('in_assignment', true);
                    },
                ])
                ->with(['positions' => function ($q) {
                    $q->where('is_head', true)->orderByDesc('in_duk')->with('employee');
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );

        return view('admin.work-units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.work-units.form', [
            'unit' => new WorkUnit(['type' => WorkUnit::TYPE_SEKSI, 'is_active' => true, 'sort_order' => 0]),
            'parentOptions' => WorkUnit::flatTree(),
        ]);
    }

    public function store(WorkUnitRequest $request)
    {
        $unit = WorkUnit::create($request->validated());

        return redirect()->route('admin.work-units.index')
            ->with('success', "Unit {$unit->name} berhasil ditambahkan.");
    }

    public function edit(WorkUnit $workUnit)
    {
        $excluded = array_merge([$workUnit->id], $workUnit->descendantIds());

        return view('admin.work-units.form', [
            'unit' => $workUnit,
            'parentOptions' => WorkUnit::flatTree()->reject(function ($unit) use ($excluded) {
                return in_array($unit->id, $excluded, true);
            }),
        ]);
    }

    public function update(WorkUnitRequest $request, WorkUnit $workUnit)
    {
        $workUnit->update($request->validated());

        return redirect()->route('admin.work-units.index')
            ->with('success', "Unit {$workUnit->name} berhasil diperbarui.");
    }

    public function destroy(WorkUnit $workUnit)
    {
        if ($workUnit->children()->exists()) {
            return back()->with('error', "Unit {$workUnit->name} masih memiliki sub-unit. Pindahkan atau hapus sub-unit terlebih dahulu.");
        }

        if ($workUnit->positions()->exists()) {
            return back()->with('error', "Unit {$workUnit->name} masih memiliki pegawai. Pindahkan pegawai ke unit lain terlebih dahulu.");
        }

        $workUnit->delete();

        return redirect()->route('admin.work-units.index')
            ->with('success', "Unit {$workUnit->name} berhasil dihapus.");
    }
}
