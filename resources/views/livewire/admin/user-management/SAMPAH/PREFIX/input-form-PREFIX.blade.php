<div x-data="{
    prefix: '{{ $isPangkal ?? '' }}',
    init() {
        if (!this.prefix) return;

        if (!$store.config?.isEdit) {
            $store.config.{{ $modelString }} = this.prefix;
        }

        this.$watch('$store.config.{{ $modelString }}', value => {
            if (!value.startsWith(this.prefix)) {
                $store.config.{{ $modelString }} = this.prefix;
            }
        });
    }
}">
    <label for="{{ $modelString }}" class="block text-sm font-medium text-gray-700">{{ $labelString }}
        @if ($isRequired ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative mt-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
            {{-- $store.config?.setEdit(0) --}}
        </div>
        <input x-model="$store.config.{{ $modelString }}" type="{{ $typeString ?? 'text' }}" id="{{ $modelString }}"
            placeholder="{{ $placeholder }}"
            class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500"
            @input="
        if (prefix && $event.target.value.length < prefix.length) {
            $store.config.{{ $modelString }} = prefix;
        }
    "
            @if (isset($numberOnly) && $numberOnly) inputmode="numeric"
        pattern="[0-9]*"
        maxlength="{{ $maxlength ?? 255 }}"
        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, {{ $maxlength ?? 255 }})"
    @else
        maxlength="{{ $maxlength ?? 255 }}" @endif>
        {{-- <div wire:loading wire:target="{{ $targetLoading }}"
            class="absolute inset-y-0 right-0 flex items-center pt-4 pr-3">
        <flux:icon name="arrow-path" class="animate-spin h-4 w-4 text-gray-400"/>
    </div> --}}
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
