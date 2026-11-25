{{--
    Kartu pegawai.
    @param \App\Models\EmployeePosition $position
    @param string $variant  'row' (horizontal, default) | 'stack' (vertikal, untuk cabang bagan)
    @param string|null $href  tautan opsional
--}}
@php
    $employee = $position->employee;
    $variant = $variant ?? 'row';
    $tag = !empty($href) ? 'a' : 'div';
@endphp

<{{ $tag }} @if (!empty($href)) href="{{ $href }}" @endif
    class="org-card org-card--{{ $variant }} {{ $position->is_head ? 'org-card--head' : '' }}">
    @if ($employee->photo_url)
        <img class="org-avatar" src="{{ $employee->photo_url }}" alt="Foto {{ $employee->name }}" loading="lazy">
    @else
        <span class="org-avatar org-avatar--initials" aria-hidden="true">{{ $employee->initials }}</span>
    @endif

    <span class="org-card__body">
        <span class="org-card__name">{{ $employee->name }}</span>
        <span class="org-card__title">
            {{ $position->title }}
        </span>
        @if ($employee->nip)
            <span class="org-card__nip">NIP {{ $employee->nip }}</span>
        @elseif ($employee->employment_type === \App\Models\Employee::TYPE_NON_ASN)
            <span class="org-card__nip">Non-ASN</span>
        @endif
    </span>
</{{ $tag }}>
