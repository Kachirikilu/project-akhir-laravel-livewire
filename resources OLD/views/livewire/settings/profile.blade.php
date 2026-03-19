<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    #[Rule(['nullable', 'image', 'max:1024'])]
    public $photo;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                ValidationRule::unique(User::class)->ignore($user->id)
            ],
        ]);
        
        $user->email = $validated['email'];
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $roleModel = null;
        if ($user->admin) {
            $roleModel = $user->admin;
        } elseif ($user->dosen) {
            $roleModel = $user->dosen;
        } elseif ($user->mahasiswa) {
            $roleModel = $user->mahasiswa;
        }

        if ($roleModel && $roleModel->name !== $validated['name']) {
            $roleModel->name = $validated['name'];
            $roleModel->save();
        }
        
        $user->save(); 

        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->forceFill(['profile_photo_path' => $path])->save();
            $this->photo = null;
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function deletePhoto(): void
    {
        $user = Auth::user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save(); 
        }
        $this->dispatch('profile-updated');
    }
    
    public function updatedPhoto()
    {
        $this->validateOnly('photo'); 
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            
            <div class="space-y-4">
                <flux:label :label="__('Profile Photo')" for="photo" />

                <div class="flex items-center space-x-4">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('New Profile Photo') }}"
                            class="h-20 w-20 rounded-full object-cover">
                    @elseif (auth()->user()->profile_photo_path)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                            class="h-20 w-20 rounded-full object-cover">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center text-xl font-semibold text-black dark:text-white">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif
                    
                    <div class="grid gap-2">
                        <input type="file" wire:model.live="photo" id="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900 dark:file:text-primary-300 dark:hover:file:bg-primary-800" />
                        
                        @error('photo') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

                        @if (auth()->user()->profile_photo_path)
                            <flux:button type="button" variant="danger" size="sm" wire:click="deletePhoto" wire:confirm="{{ __('Are you sure you want to delete your profile photo?') }}">
                                {{ __('Remove Photo') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                
                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>