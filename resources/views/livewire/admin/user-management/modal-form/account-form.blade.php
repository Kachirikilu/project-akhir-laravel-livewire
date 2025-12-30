<div>
    {{-- ****************************************************** --}}
    {{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Account Information</h4>

        {{-- 📧 Email Input --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email
                <span class="text-red-500">*</span></label>

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon.envelope variant="mini" class="text-{{ $colorRole }}-700" />
                </div>
                <input wire:model.lazy="email" type="email" id="email" placeholder="contoh@domain.com"
                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            @error('email')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- 🔒 Password Input --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password
                @if (!$isEditing)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon.lock-closed variant="mini" class="text-{{ $colorRole }}-700" />
                </div>
                <input wire:model.lazy="password" type="password" id="password"
                    placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan Password' }}"
                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            @error('password')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
