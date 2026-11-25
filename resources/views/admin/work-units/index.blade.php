@extends('admin.layout')

@section('title', 'Unit Kerja')

@section('content')
    <div class="page-header">
        <h1>Unit Kerja</h1>
        <a href="{{ route('admin.work-units.create') }}" class="btn btn-teal">
            <i class="bi bi-plus-lg"></i> Tambah Unit
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Unit</th>
                        <th>Jenis</th>
                        <th>Kepala</th>
                        <th class="text-center">Staf<br><span class="fw-normal small text-muted">DUK / Penugasan</span></th>
                        <th class="text-center">Urutan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($units as $unit)
                        <tr>
                            <td>
                                <div style="padding-left: {{ $unit->depth * 24 }}px">
                                    @if ($unit->depth > 0)
                                        <i class="bi bi-arrow-return-right text-muted"></i>
                                    @endif
                                    <span class="fw-semibold">{{ $unit->name }}</span>
                                    @if ($unit->short_name)
                                        <span class="text-muted small">({{ $unit->short_name }})</span>
                                    @endif
                                </div>
                            </td>
                            <td><span class="badge text-bg-light border">{{ $unit->type_label }}</span></td>
                            <td>
                                @forelse ($unit->positions as $head)
                                    @continue(!$head->employee)
                                    <div>
                                        {{ $head->employee->name }}
                                        @if ($head->is_acting)
                                            <span class="badge text-bg-warning">Plt</span>
                                        @endif
                                        @if ($head->scope === \App\Models\EmployeePosition::SCOPE_DUK_ONLY)
                                            <span class="badge text-bg-light border">Hanya DUK</span>
                                        @elseif ($head->scope === \App\Models\EmployeePosition::SCOPE_ASSIGNMENT_ONLY)
                                            <span class="badge text-bg-primary">Nota tugas</span>
                                        @endif
                                    </div>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('admin.employees.index', ['unit' => $unit->id]) }}"
                                    title="Staf versi DUK / versi penugasan">{{ $unit->staff_duk_count }} / {{ $unit->staff_assignment_count }}</a>
                            </td>
                            <td class="text-center">{{ $unit->sort_order }}</td>
                            <td>
                                @if ($unit->is_active)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.employees.create', ['unit' => $unit->id]) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Tambah pegawai di unit ini">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                                <a href="{{ route('admin.work-units.edit', $unit) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.work-units.destroy', $unit) }}" method="POST" class="d-inline"
                                    data-confirm="Hapus unit {{ $unit->name }}?">
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
                            <td colspan="7" class="text-center text-muted py-4">Belum ada unit kerja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-muted small mt-3 mb-0">
        <i class="bi bi-info-circle"></i>
        Unit berjenis <b>Sub Bagian</b> tampil sebagai cabang samping pada bagan. Urutan menentukan posisi dari kiri ke kanan.
    </p>
@endsection
