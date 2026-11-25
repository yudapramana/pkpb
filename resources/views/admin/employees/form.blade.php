@extends('admin.layout')

@section('title', $employee->exists ? 'Ubah Pegawai' : 'Tambah Pegawai')

@php
    $rows = old('positions', $positionRows);
    if (empty($rows)) {
        $rows = [['work_unit_id' => null, 'title' => '', 'is_head' => false, 'is_acting' => false, 'sort_order' => 0]];
    }
@endphp

@section('content')
    <div class="page-header">
        <h1>{{ $employee->exists ? 'Ubah Pegawai' : 'Tambah Pegawai' }}</h1>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Periksa kembali isian yang ditandai merah.
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
        action="{{ $employee->exists ? route('admin.employees.update', $employee) : route('admin.employees.store') }}">
        @csrf
        @if ($employee->exists)
            @method('PUT')
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Data Pegawai</div>
            <div class="card-body p-4">
                <div class="row g-4">
                    {{-- Foto --}}
                    <div class="col-md-3 text-center">
                        <div class="mb-2">
                            @if ($employee->photo_url)
                                <img src="{{ $employee->photo_url }}" id="photo-preview" alt="Foto {{ $employee->name }}"
                                    class="rounded-circle" style="width: 128px; height: 128px; object-fit: cover">
                            @else
                                <img src="" id="photo-preview" alt="" class="rounded-circle d-none"
                                    style="width: 128px; height: 128px; object-fit: cover">
                                <span id="photo-placeholder" class="avatar" style="width: 128px; height: 128px; font-size: 2rem">
                                    {{ $employee->exists ? $employee->initials : '?' }}
                                </span>
                            @endif
                        </div>
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
                            class="form-control form-control-sm @error('photo') is-invalid @enderror">
                        <div class="form-text">JPG/PNG/WEBP, maks. 2 MB. Sebaiknya persegi.</div>
                        @error('photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($employee->photo)
                            <div class="form-check text-start mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
                                <label class="form-check-label small" for="remove_photo">Hapus foto</label>
                            </div>
                        @endif
                    </div>

                    {{-- Identitas --}}
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap (dengan gelar) <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" maxlength="150" required
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $employee->name) }}" placeholder="H. Yufrizal, S.Ag., M.H.I.">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="employment_type" class="form-label">Jenis Kepegawaian <span class="text-danger">*</span></label>
                                <select id="employment_type" name="employment_type" required
                                    class="form-select @error('employment_type') is-invalid @enderror">
                                    @foreach (\App\Models\Employee::typeLabels() as $value => $label)
                                        <option value="{{ $value }}" {{ old('employment_type', $employee->employment_type) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="nip" class="form-label">NIP <span class="text-danger" id="nip-required">*</span></label>
                                <input type="text" id="nip" name="nip" inputmode="numeric" maxlength="21"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip', $employee->nip) }}" placeholder="18 digit">
                                <div class="form-text">Boleh kosong untuk Non-ASN.</div>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif (tampil di halaman publik)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Jabatan &amp; Unit Kerja</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="add-position">
                    <i class="bi bi-plus-lg"></i> Tambah Jabatan
                </button>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small">
                    Satu pegawai bisa memegang lebih dari satu jabatan, misalnya Kepala Seksi sekaligus Plt Kepala Seksi lain.
                    Centang <b>Kepala unit</b> agar tampil di bagan. Staf diurutkan otomatis berdasarkan tahun masuk
                    (NIP digit 9–12), lalu tanggal lahir.
                    <br>
                    <b>Berlaku di</b>: jika pegawai ditugaskan ke unit lain dengan nota tugas, buat 2 baris:
                    unit sesuai DUK dengan pilihan <i>Hanya DUK</i>, dan unit tempat bertugas dengan pilihan
                    <i>Hanya penugasan</i>. Jabatan Plt juga diisi <i>Hanya penugasan</i>.
                </p>

                @error('positions')
                    <div class="alert alert-danger py-2">{{ $message }}</div>
                @enderror

                <div id="positions" data-next-index="{{ count($rows) }}">
                    @foreach (array_values($rows) as $i => $row)
                        @include('admin.employees._position-row', ['index' => $i, 'row' => $row, 'units' => $units])
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-teal">
            <i class="bi bi-save"></i> Simpan
        </button>
    </form>

    <template id="position-template">
        @include('admin.employees._position-row', ['index' => '__INDEX__', 'row' => [], 'units' => $units])
    </template>
@endsection

@push('scripts')
    <script>
        (function () {
            var container = document.getElementById('positions');
            var template = document.getElementById('position-template');

            document.getElementById('add-position').addEventListener('click', function () {
                var index = parseInt(container.getAttribute('data-next-index'), 10);
                container.setAttribute('data-next-index', index + 1);
                container.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, index));
                container.lastElementChild.querySelector('select').focus();
            });

            container.addEventListener('click', function (event) {
                var button = event.target.closest('.js-remove-position');
                if (!button) return;
                if (container.querySelectorAll('.position-row').length <= 1) {
                    alert('Minimal harus ada 1 jabatan.');
                    return;
                }
                button.closest('.position-row').remove();
            });

            // "Plt" hanya berlaku untuk kepala unit.
            container.addEventListener('change', function (event) {
                if (!event.target.classList.contains('js-is-head')) return;
                var acting = event.target.closest('.position-row').querySelector('.js-is-acting');
                acting.disabled = !event.target.checked;
                if (!event.target.checked) acting.checked = false;
            });

            // NIP wajib kecuali Non-ASN.
            var type = document.getElementById('employment_type');
            var nipMark = document.getElementById('nip-required');
            function syncNipRequired() {
                nipMark.classList.toggle('d-none', type.value === '{{ \App\Models\Employee::TYPE_NON_ASN }}');
            }
            type.addEventListener('change', syncNipRequired);
            syncNipRequired();

            // Pratinjau foto sebelum diunggah.
            var photo = document.getElementById('photo');
            var preview = document.getElementById('photo-preview');
            var placeholder = document.getElementById('photo-placeholder');
            photo.addEventListener('change', function () {
                if (!photo.files || !photo.files[0]) return;
                preview.src = URL.createObjectURL(photo.files[0]);
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            });
        })();
    </script>
@endpush
