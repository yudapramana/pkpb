{{--
    Satu baris jabatan di form pegawai.
    @param int|string $index   index baris, atau '__INDEX__' untuk template JS
    @param array $row          work_unit_id, title, is_head, is_acting, scope, sort_order
    @param \Illuminate\Support\Collection $units
--}}
@php
    $field = function ($name) use ($index) {
        return "positions[$index][$name]";
    };
    $error = function ($name) use ($index, $errors) {
        return $errors->first("positions.$index.$name");
    };
    $uid = 'pos-' . $index;
@endphp

<div class="position-row border rounded p-3 mb-2 bg-light-subtle">
    <div class="row g-2 align-items-start">
        <div class="col-md-4">
            <label class="form-label small mb-1" for="{{ $uid }}-unit">Unit Kerja <span class="text-danger">*</span></label>
            <select id="{{ $uid }}-unit" name="{{ $field('work_unit_id') }}" class="form-select {{ $error('work_unit_id') ? 'is-invalid' : '' }}" required>
                <option value="">- Pilih unit -</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" {{ (string) ($row['work_unit_id'] ?? '') === (string) $unit->id ? 'selected' : '' }}>
                        {{ str_repeat('— ', $unit->depth) }}{{ $unit->name }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback">{{ $error('work_unit_id') }}</div>
        </div>

        <div class="col-md-3">
            <label class="form-label small mb-1" for="{{ $uid }}-title">Jabatan <span class="text-danger">*</span></label>
            <input type="text" id="{{ $uid }}-title" name="{{ $field('title') }}" maxlength="150" required
                class="form-control {{ $error('title') ? 'is-invalid' : '' }}"
                value="{{ $row['title'] ?? '' }}" placeholder="Analis SDM Aparatur Ahli Muda">
            <div class="invalid-feedback">{{ $error('title') }}</div>
        </div>

        <div class="col-md-2">
            <label class="form-label small mb-1" for="{{ $uid }}-scope">Berlaku di</label>
            <select id="{{ $uid }}-scope" name="{{ $field('scope') }}" class="form-select {{ $error('scope') ? 'is-invalid' : '' }}">
                @foreach (\App\Models\EmployeePosition::scopeLabels() as $value => $label)
                    <option value="{{ $value }}" {{ ($row['scope'] ?? \App\Models\EmployeePosition::SCOPE_BOTH) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">{{ $error('scope') }}</div>
        </div>

        <div class="col-md-2 col-7">
            <span class="form-label small mb-1 d-block">Peran</span>
            <div class="form-check">
                <input class="form-check-input js-is-head {{ $error('is_head') ? 'is-invalid' : '' }}" type="checkbox"
                    id="{{ $uid }}-head" name="{{ $field('is_head') }}" value="1" {{ !empty($row['is_head']) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $uid }}-head">Kepala unit</label>
            </div>
            <div class="form-check">
                <input class="form-check-input js-is-acting" type="checkbox"
                    id="{{ $uid }}-acting" name="{{ $field('is_acting') }}" value="1"
                    {{ !empty($row['is_acting']) ? 'checked' : '' }} {{ empty($row['is_head']) ? 'disabled' : '' }}>
                <label class="form-check-label" for="{{ $uid }}-acting">Plt</label>
            </div>
        </div>

        <div class="col-md-1 col-5 text-end">
            <span class="form-label small mb-1 d-block">&nbsp;</span>
            <button type="button" class="btn btn-outline-danger js-remove-position" title="Hapus jabatan ini">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    @if ($error('is_head'))
        <div class="text-danger small mt-2">{{ $error('is_head') }}</div>
    @endif
</div>
