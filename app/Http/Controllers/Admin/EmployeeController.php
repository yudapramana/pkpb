<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $unitId = $request->query('unit');
        $status = $request->query('status');

        $employees = Employee::query()
            ->with(['positions' => function ($q) {
                $q->orderByDesc('is_head')->with('workUnit');
            }])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->when($unitId, function ($q) use ($unitId) {
                $q->whereHas('positions', function ($q) use ($unitId) {
                    $q->where('work_unit_id', $unitId);
                });
            })
            ->when($status === 'active', function ($q) {
                $q->where('is_active', true);
            })
            ->when($status === 'inactive', function ($q) {
                $q->where('is_active', false);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.employees.index', [
            'employees' => $employees,
            'units' => WorkUnit::flatTree(),
            'filters' => ['q' => $search, 'unit' => $unitId, 'status' => $status],
        ]);
    }

    public function create(Request $request)
    {
        $employee = new Employee(['employment_type' => Employee::TYPE_PNS, 'is_active' => true]);

        return view('admin.employees.form', [
            'employee' => $employee,
            'units' => WorkUnit::flatTree(),
            // Bisa diisi dari tautan "Tambah pegawai" di unit tertentu: ?unit=ID
            'positionRows' => [[
                'work_unit_id' => $request->query('unit'),
                'title' => '',
                'is_head' => false,
                'is_acting' => false,
                'scope' => EmployeePosition::SCOPE_BOTH,
                'sort_order' => 0,
            ]],
        ]);
    }

    public function store(EmployeeRequest $request)
    {
        $employee = DB::transaction(function () use ($request) {
            $employee = Employee::create($request->employeeData());
            $this->syncPositions($employee, $request->positionsData());

            return $employee;
        });

        $this->handlePhoto($request, $employee);

        return redirect()->route('admin.employees.index')
            ->with('success', "Pegawai {$employee->name} berhasil ditambahkan.");
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.form', [
            'employee' => $employee,
            'units' => WorkUnit::flatTree(),
            'positionRows' => $employee->positions()
                ->orderByDesc('is_head')
                ->orderByDesc('in_duk')
                ->get()
                ->map(function ($position) {
                    return [
                        'work_unit_id' => $position->work_unit_id,
                        'title' => $position->title,
                        'is_head' => $position->is_head,
                        'is_acting' => $position->is_acting,
                        'scope' => $position->scope,
                        'sort_order' => $position->sort_order,
                    ];
                })
                ->all(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {
            $employee->update($request->employeeData());
            $this->syncPositions($employee, $request->positionsData());
        });

        $this->handlePhoto($request, $employee);

        return redirect()->route('admin.employees.index')
            ->with('success', "Data {$employee->name} berhasil diperbarui.");
    }

    public function destroy(Employee $employee)
    {
        if ($employee->user()->exists()) {
            return back()->with('error', "{$employee->name} memiliki akun admin dan tidak bisa dihapus.");
        }

        DB::transaction(function () use ($employee) {
            $employee->positions()->delete();
            $employee->delete(); // soft delete
        });

        return redirect()->route('admin.employees.index')
            ->with('success', "Pegawai {$employee->name} berhasil dihapus.");
    }

    /** Samakan penempatan pegawai dengan baris jabatan di form. */
    private function syncPositions(Employee $employee, array $rows)
    {
        $unitIds = [];

        foreach ($rows as $row) {
            $employee->positions()->updateOrCreate(
                ['work_unit_id' => $row['work_unit_id']],
                $row
            );
            $unitIds[] = $row['work_unit_id'];
        }

        $employee->positions()->whereNotIn('work_unit_id', $unitIds)->delete();
    }

    private function handlePhoto(EmployeeRequest $request, Employee $employee)
    {
        $disk = Storage::disk('public');
        $oldPhoto = $employee->photo;

        if ($request->hasFile('photo')) {
            $employee->update(['photo' => $request->file('photo')->store('employees', 'public')]);
        } elseif ($request->boolean('remove_photo')) {
            $employee->update(['photo' => null]);
        }

        if ($oldPhoto && $oldPhoto !== $employee->photo) {
            $disk->delete($oldPhoto);
        }
    }
}
