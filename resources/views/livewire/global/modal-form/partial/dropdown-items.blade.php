<div class="flex flex-col mr-4">
    <span class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
    <div class="text-[var(--contrast-main-text) font-medium text-xs flex items-center mt-1">
        <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                {{ $x['id'] }}</span></span>
        <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
        <span>{{ $x['kode'] }}</span>
        @if ($typeX2String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>{{ $x[$typeX2String] }}</span>
        @endif
        @if ($typeX3String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>
            @if ($typeX3String == 'bobot' || $typeX3String == 'total_bobot')
                Bobot: {{ $x[$typeX3String] }}%
            @else
                {{ $x[$typeX3String] }}
            @endif
            </span>
        @endif
    </div>
</div>
