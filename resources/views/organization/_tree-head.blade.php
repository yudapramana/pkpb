{{--
    Kotak kepala unit: pita nama unit + nama, jabatan, NIP kepala.
    @param \App\Models\WorkUnit $unit
--}}
<div class="t-head">
    <div class="t-head__unit">{{ $unit->name }}</div>
    <div class="t-head__body">
        @if ($unit->headPosition)
            <div class="t-item__name">{{ $unit->headPosition->employee->name }}</div>
            <div class="t-item__title">{{ $unit->headPosition->title }}</div>
            @if ($unit->headPosition->employee->nip)
                <div class="t-item__nip">NIP {{ $unit->headPosition->employee->nip }}</div>
            @endif
        @else
            <div class="t-item__title">Kepala belum ditetapkan</div>
        @endif
    </div>
</div>
