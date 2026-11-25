@extends('admin.layout')

@section('title', $item->exists ? 'Ubah Item Portal' : 'Tambah Item Portal')

@section('content')
    <div class="page-header">
        <h1>{{ $item->exists ? 'Ubah Item Portal' : 'Tambah Item Portal' }}</h1>
        <a href="{{ route('admin.portal-items.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 860px">
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data"
                action="{{ $item->exists ? route('admin.portal-items.update', $item) : route('admin.portal-items.store') }}">
                @csrf
                @if ($item->exists)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    {{-- Ikon --}}
                    <div class="col-md-4 text-center">
                        <div class="border rounded-3 p-3 mb-2 bg-light d-flex align-items-center justify-content-center" style="height: 150px">
                            <img src="{{ $item->exists ? $item->image_src : \App\Models\PortalItem::DEFAULT_IMAGE }}"
                                id="image-preview" alt="Pratinjau ikon" style="max-height: 110px; max-width: 100%; object-fit: contain">
                        </div>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp,image/gif"
                            class="form-control form-control-sm @error('image') is-invalid @enderror">
                        <div class="form-text">JPG, PNG, WEBP atau GIF, maks. 2 MB. Disarankan PNG transparan persegi.</div>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($item->image_path)
                            <div class="form-check mt-2 text-start">
                                <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                                <label class="form-check-label small" for="remove_image">Hapus gambar unggahan</label>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" maxlength="100" required
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $item->title) }}" placeholder="PTSP Kemenag">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="destination_url" class="form-label">URL Tujuan</label>
                            <input type="url" id="destination_url" name="destination_url" maxlength="2000"
                                class="form-control @error('destination_url') is-invalid @enderror"
                                value="{{ old('destination_url', $item->destination_url) }}" placeholder="https://ptsp.kemenagpessel.com">
                            <div class="form-text">Kosongkan bila layanan belum tersedia; item tampil dengan label "Segera hadir".</div>
                            @error('destination_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">URL Gambar (opsional)</label>
                            <input type="text" id="image_url" name="image_url" maxlength="2000"
                                class="form-control @error('image_url') is-invalid @enderror"
                                value="{{ old('image_url', $item->image_url) }}" placeholder="https://... atau /images/portal/ikon.svg">
                            <div class="form-text">Dipakai bila tidak ada gambar unggahan. Boleh URL lengkap atau path lokal di folder <code>public</code>.</div>
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-sm-4">
                                <label for="sort_order" class="form-label">Urutan</label>
                                <input type="number" id="sort_order" name="sort_order" min="0"
                                    class="form-control @error('sort_order') is-invalid @enderror"
                                    value="{{ old('sort_order', $item->sort_order) }}">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-8 d-flex flex-column justify-content-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif (tampil di halaman depan)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_modal" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_modal" name="is_modal" value="1"
                                        {{ old('is_modal', $item->is_modal) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_modal">Buka dalam pop-up</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-text mb-4">
                            Pop-up menampilkan situs tujuan di dalam halaman portal. Beberapa situs menolak ditampilkan
                            seperti ini, sehingga tombol "Buka di tab baru" tetap tersedia di pop-up.
                        </div>

                        <button type="submit" class="btn btn-teal">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var input = document.getElementById('image');
            var preview = document.getElementById('image-preview');
            input.addEventListener('change', function () {
                if (input.files && input.files[0]) {
                    preview.src = URL.createObjectURL(input.files[0]);
                }
            });
        })();
    </script>
@endpush
