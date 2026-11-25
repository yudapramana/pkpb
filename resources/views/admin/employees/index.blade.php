@extends('admin.layout')

@section('title', 'Pegawai')

@section('content')
    <div class="page-header">
        <h1>Pegawai <span class="text-muted fs-6 fw-normal">({{ $employees->total() }})</span></h1>
        <a href="{{ route('admin.employees.create', array_filter(['unit' => $filters['unit']])) }}" class="btn btn-teal">
            <i class="bi bi-plus-lg"></i> Tambah Pegawai
        </a>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-5">
                <label for="q" class="form-label small mb-1">Cari</label>
                <input type="search" id="q" name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="Nama atau NIP">
            </div>
            <div class="col-md-4">
                <label for="unit" class="form-label small mb-1">Unit Kerja</label>
                <select id="unit" name="unit" class="form-select">
                    <option value="">Semua unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ (string) $filters['unit'] === (string) $unit->id ? 'selected' : '' }}>
                            {{ str_repeat('— ', $unit->depth) }}{{ $unit->short_name ?: $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label small mb-1">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-outline-secondary" title="Terapkan filter"><i class="bi bi-funnel"></i></button>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pegawai</th>
                        <th>Jabatan &amp; Unit</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($employee->photo_url)
                                        <img src="{{ $employee->photo_url }}" class="avatar" alt="">
                                    @else
                                        <span class="avatar">{{ $employee->initials }}</span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $employee->name }}</div>
                                        <div class="text-muted small">{{ $employee->nip ? 'NIP ' . $employee->nip : '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach ($employee->positions as $position)
                                    <div class="small">
                                        {{ $position->title }}
                                        @if ($position->is_head)
                                            <span class="badge text-bg-info">Kepala</span>
                                        @endif
                                        @if ($position->is_acting)
                                            <span class="badge text-bg-warning">Plt</span>
                                        @endif
                                        @if ($position->scope === \App\Models\EmployeePosition::SCOPE_DUK_ONLY)
                                            <span class="badge text-bg-light border" title="Penempatan sesuai DUK, sedang ditugaskan di unit lain">Hanya DUK</span>
                                        @elseif ($position->scope === \App\Models\EmployeePosition::SCOPE_ASSIGNMENT_ONLY)
                                            <span class="badge text-bg-primary" title="Penempatan berdasarkan nota tugas / surat perintah">Nota tugas</span>
                                        @endif
                                        <span class="text-muted">&middot; {{ optional($position->workUnit)->short_name ?: optional($position->workUnit)->name }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td><span class="badge text-bg-light border">{{ $employee->employment_type_label }}</span></td>
                            <td>
                                @if ($employee->is_active)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline"
                                    data-confirm="Hapus pegawai {{ $employee->name }}?">
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
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada pegawai yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $employees->links() }}
    </div>
@endsection
