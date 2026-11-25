@extends('admin.layout')

@section('title', $unit->exists ? 'Ubah Unit Kerja' : 'Tambah Unit Kerja')

@section('content')
    <div class="page-header">
        <h1>{{ $unit->exists ? 'Ubah Unit Kerja' : 'Tambah Unit Kerja' }}</h1>
        <a href="{{ route('admin.work-units.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 760px">
        <div class="card-body p-4">
            <form method="POST"
                action="{{ $unit->exists ? route('admin.work-units.update', $unit) : route('admin.work-units.store') }}">
                @csrf
                @if ($unit->exists)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Unit <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" maxlength="150" required
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $unit->name) }}" placeholder="Seksi Pendidikan Madrasah">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="short_name" class="form-label">Nama Singkat</label>
                        <input type="text" id="short_name" name="short_name" maxlength="50"
                            class="form-control @error('short_name') is-invalid @enderror"
                            value="{{ old('short_name', $unit->short_name) }}" placeholder="Penmad">
                        <div class="form-text">Dipakai sebagai judul kelompok staf di halaman publik.</div>
                        @error('short_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Jenis Unit <span class="text-danger">*</span></label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                            @foreach (\App\Models\WorkUnit::typeLabels() as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $unit->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Unit Induk</label>
                    <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                        <option value="">- Tidak ada (unit puncak) -</option>
                        @foreach ($parentOptions as $option)
                            <option value="{{ $option->id }}" {{ (string) old('parent_id', $unit->parent_id) === (string) $option->id ? 'selected' : '' }}>
                                {{ str_repeat('— ', $option->depth) }}{{ $option->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="sort_order" class="form-label">Urutan</label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                            class="form-control @error('sort_order') is-invalid @enderror"
                            value="{{ old('sort_order', $unit->sort_order) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif (tampil di halaman publik)</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-teal">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
