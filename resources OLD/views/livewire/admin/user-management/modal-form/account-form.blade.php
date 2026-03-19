<div>
    {{-- ****************************************************** --}}
    {{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Account Information</h4>

        {{-- 📧 Email Input --}}
        @include('livewire.admin.user-management.modal-form.partial.input', [
            'colorIcon' => $colorIcon,
            'labelString' => 'Email',
            'modelString' => 'email',
            'typeString' => 'email',
            'placeholder' => 'contoh@domain.com',
            'message' => $errors->first('email'),
            'isRequired' => 1
        ])

        {{-- 🔒 Password Input --}}
        @include('livewire.admin.user-management.modal-form.partial.input', [
            'colorIcon' => $colorIcon,
            'labelString' => 'Password',
            'modelString' => 'password',
            'typeString' => 'password',
            'placeholder' => $isEditing ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan Password',
            'message' => $errors->first('password'),
            'isRequired' => !$isEditing
        ])
    </div>
</div>
