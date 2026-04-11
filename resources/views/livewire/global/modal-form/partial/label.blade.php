@if (isset($noLabel) == 0)
    <label for="{{ $modelString }}" class="block text-sm font-medium mb-2 text-[var(--contrast-main-text)]">
        {{ $nameX2String ?? $nameXString }}
        @if ($isRequired ?? true)
            <span class="text-red-500">*</span>
        @endif
    </label>
@endif
