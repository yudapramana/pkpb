@extends('admin.layout')

@section('title', 'Item Portal')

@section('content')
    <div class="page-header">
        <div>
            <h1>Item Portal</h1>
            <div class="text-muted small">Menu layanan yang tampil di halaman depan, diurutkan berdasarkan kolom Urutan.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i> Lihat Halaman Depan
            </a>
            <a href="{{ route('admin.portal-items.create') }}" class="btn btn-teal">
                <i class="bi bi-plus-lg"></i> Tambah Item
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 80px">Urutan</th>
                        <th style="width: 72px">Ikon</th>
                        <th>Judul</th>
                        <th>URL Tujuan</th>
                        <th>Tampilan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="text-center">{{ $item->sort_order }}</td>
                            <td>
                                <img src="{{ $item->image_src }}" alt="" width="44" height="44" style="object-fit: contain" loading="lazy">
                            </td>
                            <td class="fw-semibold">{{ $item->title }}</td>
                            <td style="max-width: 320px">
                                @if ($item->has_link)
                                    <a href="{{ $item->destination_url }}" target="_blank" rel="noopener"
                                        class="d-inline-block text-truncate" style="max-width: 100%">{{ $item->destination_url }}</a>
                                @else
                                    <span class="badge text-bg-light border">Belum ada tautan</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->is_modal)
                                    <span class="badge text-bg-info">Pop-up</span>
                                @else
                                    <span class="badge text-bg-light border">Tab baru</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.portal-items.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.portal-items.destroy', $item) }}" method="POST" class="d-inline"
                                    data-confirm="Hapus item {{ $item->title }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada item portal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
