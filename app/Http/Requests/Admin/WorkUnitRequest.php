<?php

namespace App\Http\Requests\Admin;

use App\Models\WorkUnit;
use Illuminate\Validation\Rule;

class WorkUnitRequest extends AdminFormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'parent_id' => $this->input('parent_id') ?: null,
            'short_name' => $this->filled('short_name') ? trim($this->input('short_name')) : null,
            'sort_order' => $this->filled('sort_order') ? $this->input('sort_order') : 0,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules()
    {
        $unit = $this->route('workUnit');

        // Unit tidak boleh menjadi induk dirinya sendiri atau turunannya.
        $forbiddenParents = $unit ? array_merge([$unit->id], $unit->descendantIds()) : [];

        return [
            'parent_id' => ['nullable', 'integer', 'exists:work_units,id', Rule::notIn($forbiddenParents)],
            'name' => ['required', 'string', 'max:150'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'type' => ['required', Rule::in(array_keys(WorkUnit::typeLabels()))],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('parent_id')) {
                return;
            }

            // Halaman publik hanya punya satu puncak bagan.
            $unit = $this->route('workUnit');
            $otherRoot = WorkUnit::root()
                ->when($unit, function ($q) use ($unit) {
                    $q->where('id', '!=', $unit->id);
                })
                ->first();

            if ($otherRoot) {
                $validator->errors()->add(
                    'parent_id',
                    "Unit induk wajib dipilih. Unit puncak sudah ada: {$otherRoot->name}."
                );
            }
        });
    }

    public function attributes()
    {
        return [
            'parent_id' => 'Unit induk',
            'name' => 'Nama unit',
            'short_name' => 'Nama singkat',
            'type' => 'Jenis unit',
            'sort_order' => 'Urutan',
            'is_active' => 'Status',
        ];
    }
}
