<div>
    <label for="{{ $modelString }}" class="block text-sm font-medium text-gray-700">{{ $labelString }}
        @if ($isRequired ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative mt-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.envelope variant="mini" x-bind:class="$store.config?.colorIcon" />
            {{-- $store.config?.setEdit(0) --}}
        </div>
        <input wire:model.lazy="{{ $modelString }}" x-bind:value="$store.config?.isEdit ? $el.value : ''"
            type="{{ $typeString ?? 'text' }}" id="{{ $modelString }}" placeholder="{{ $placeholder }}"
            class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500"
            @if (isset($numberOnly) && $numberOnly) inputmode="numeric" pattern="[0-9]*" maxlength="{{ $maxlength ?? 255 }}"
        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, {{ $maxlength ?? 255 }})"
    @else
        maxlength="{{ $maxlength ?? 255 }}" @endif>
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
