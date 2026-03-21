<div>
    {{-- ****************************************************** --}}
    {{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white dark:bg-neutral-800 shadow-sm rounded-lg border border-gray-100 dark:border-neutral-700 space-y-4 transition-colors duration-300">
        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 border-b dark:border-neutral-700 pb-2">Account Information</h4>

        {{-- 📧 Email Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Email',
            'modelString' => 'email',
            'typeString' => 'email',
            'iconString' => 'envelope',
            'placeholder' => 'contoh@domain.com',
            'message' => $errors->first('email'),
            'isRequired' => 1
        ])

        {{-- 🔒 Password Input --}}
        <template x-if="$store.config?.isEdit == 0" x-cloak>
            @include('livewire.admin.global.modal-form.input-form', [
                // 'colorIcon' => $colorIcon,
                'labelString' => 'Password',
                'modelString' => 'password',
                'typeString' => 'password',
                'iconString' => 'lock-closed',
                'placeholder' => 'Masukkan Password',
                'message' => $errors->first('password'),
                'isRequired' => 1
            ])
        </template>
        <template x-if="$store.config?.isEdit == 1" x-cloak>
            @include('livewire.admin.global.modal-form.input-form', [
                // 'colorIcon' => $colorIcon,
                'labelString' => 'Password',
                'modelString' => 'password',
                'typeString' => 'password',
                'iconString' => 'lock-closed',
                'placeholder' => 'Kosongkan jika tidak ingin diubah',
                'message' => $errors->first('password'),
                'isRequired' => 0
            ])
        </template>
    </div>
</div>
