{{--
    Kotak teks pegawai (tanpa foto) untuk bagan pohon.
    @param \App\Models\EmployeePosition $position
--}}
<div class="t-item">
    <div class="t-item__name">{{ $position->employee->name }}</div>
    <div class="t-item__title">{{ $position->title }}</div>
    <div class="t-item__nip">{{ $position->employee->nip ? 'NIP ' . $position->employee->nip : 'Non-ASN' }}</div>
    @if ($note = $position->versionNote($version))
        <div class="t-item__note">{{ $note }}</div>
    @endif
</div>
