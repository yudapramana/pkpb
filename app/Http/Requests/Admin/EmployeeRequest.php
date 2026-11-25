<?php

namespace App\Http\Requests\Admin;

use App\Models\Employee;
use App\Models\EmployeePosition;
use Illuminate\Validation\Rule;

class EmployeeRequest extends AdminFormRequest
{
    protected function prepareForValidation()
    {
        $nip = preg_replace('/\s+/', '', (string) $this->input('nip'));

        // Index baris jabatan dirapikan (0..n) agar pesan error cocok dengan baris di form.
        $positions = array_values(array_map(function ($row) {
            return [
                'work_unit_id' => $row['work_unit_id'] ?? null,
                'title' => isset($row['title']) ? trim($row['title']) : null,
                'is_head' => !empty($row['is_head']),
                'is_acting' => !empty($row['is_acting']),
                'scope' => $row['scope'] ?? EmployeePosition::SCOPE_BOTH,
                'sort_order' => isset($row['sort_order']) && $row['sort_order'] !== '' ? $row['sort_order'] : 0,
            ];
        }, (array) $this->input('positions', [])));

        $this->merge([
            'nip' => $nip !== '' ? $nip : null,
            'is_active' => $this->boolean('is_active'),
            'remove_photo' => $this->boolean('remove_photo'),
            'positions' => $positions,
        ]);
    }

    public function rules()
    {
        $employee = $this->route('employee');

        return [
            'name' => ['required', 'string', 'max:150'],
            'employment_type' => ['required', Rule::in(array_keys(Employee::typeLabels()))],
            'nip' => [
                'nullable',
                'required_unless:employment_type,' . Employee::TYPE_NON_ASN,
                'digits:18',
                Rule::unique('employees', 'nip')->ignore($employee),
            ],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['boolean'],

            'positions' => ['required', 'array', 'min:1'],
            'positions.*.work_unit_id' => ['required', 'integer', 'distinct', 'exists:work_units,id'],
            'positions.*.title' => ['required', 'string', 'max:150'],
            'positions.*.is_head' => ['boolean'],
            'positions.*.is_acting' => ['boolean'],
            'positions.*.scope' => ['required', Rule::in(array_keys(EmployeePosition::scopeLabels()))],
            'positions.*.sort_order' => ['integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $employee = $this->route('employee');

            foreach ((array) $this->input('positions') as $i => $row) {
                if (empty($row['is_head']) || empty($row['work_unit_id'])) {
                    continue;
                }

                // Satu unit hanya boleh punya satu kepala pada setiap versi (DUK / penugasan).
                $flags = EmployeePosition::scopeToFlags($row['scope'] ?? EmployeePosition::SCOPE_BOTH);

                $existing = EmployeePosition::query()
                    ->where('work_unit_id', $row['work_unit_id'])
                    ->where('is_head', true)
                    ->where(function ($q) use ($flags) {
                        if ($flags['in_duk']) {
                            $q->orWhere('in_duk', true);
                        }
                        if ($flags['in_assignment']) {
                            $q->orWhere('in_assignment', true);
                        }
                    })
                    ->when($employee, function ($q) use ($employee) {
                        $q->where('employee_id', '!=', $employee->id);
                    })
                    ->whereHas('employee')
                    ->with(['employee', 'workUnit'])
                    ->first();

                if ($existing) {
                    $validator->errors()->add(
                        "positions.$i.is_head",
                        "{$existing->workUnit->name} sudah memiliki kepala: {$existing->employee->name}"
                        . " ({$existing->scope_label}). Ubah dulu jabatan pegawai tersebut."
                    );
                }
            }
        });
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'nip.required_unless' => 'NIP wajib diisi untuk pegawai PNS/PPPK.',
            'positions.required' => 'Minimal 1 jabatan wajib diisi.',
        ]);
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'employment_type' => 'Jenis kepegawaian',
            'nip' => 'NIP',
            'is_active' => 'Status',
            'photo' => 'Foto',
            'positions' => 'jabatan',
            'positions.*.work_unit_id' => 'Unit kerja',
            'positions.*.title' => 'Jabatan',
            'positions.*.is_head' => 'Kepala unit',
            'positions.*.is_acting' => 'Plt',
            'positions.*.scope' => 'Berlaku di',
            'positions.*.sort_order' => 'Urutan',
        ];
    }

    /** Kolom tabel employees. */
    public function employeeData()
    {
        return $this->only(['name', 'nip', 'employment_type', 'is_active']);
    }

    /** Baris jabatan yang sudah dinormalisasi. */
    public function positionsData()
    {
        return array_map(function ($row) {
            return array_merge([
                'work_unit_id' => (int) $row['work_unit_id'],
                'title' => $row['title'],
                'is_head' => (bool) $row['is_head'],
                'is_acting' => (bool) $row['is_head'] && (bool) $row['is_acting'], // Plt hanya untuk kepala
                'sort_order' => (int) $row['sort_order'],
            ], EmployeePosition::scopeToFlags($row['scope']));
        }, $this->input('positions', []));
    }
}
