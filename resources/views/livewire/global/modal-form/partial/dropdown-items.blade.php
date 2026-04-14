@php
    $itemLabel = data_get($x, $typeXString, '');
    $itemId = data_get($x, 'id', '');
    $itemKode = data_get($x, 'kode', '');
    $itemLabel2 = isset($typeX2String) ? data_get($x, $typeX2String, '') : null;
    $itemLabel3 = isset($typeX3String) ? data_get($x, $typeX3String, '') : null;
@endphp

<div class="flex flex-col mr-4">
    <span class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $itemLabel }}</span>
    <div class="text-[var(--contrast-main-text) font-medium text-xs flex items-center mt-1">
        <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                {{ $itemId }}</span></span>
        <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
        <span>{{ $itemKode }}</span>
        @if ($typeX2String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>{{ $itemLabel2 }}</span>
        @endif
        @if ($typeX3String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>
            @if ($typeX3String == 'bobot' || $typeX3String == 'total_bobot')
                Bobot: {{ $itemLabel3 }}%
            @else
                {{ $itemLabel3 }}
            @endif
            </span>
        @endif
    </div>
</div>
