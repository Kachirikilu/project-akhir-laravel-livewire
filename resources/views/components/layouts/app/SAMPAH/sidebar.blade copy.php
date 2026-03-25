<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-sky-600 bg-sky-800 dark:border-sky-800 dark:bg-sky-950">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            {{-- Tambahkan !text-white/70 agar heading juga terlihat terang --}}
            <flux:navlist.group :heading="__('Platform')" class="grid !text-white/70">

                {{-- Gunakan class di bawah ini untuk semua item --}}
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    class="!text-white/90 hover:!text-white hover:bg-white/10 data-[current]:!bg-white/20 data-[current]:!text-white"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                <flux:navlist.item icon="user" :href="route('user-management')"
                    :current="request()->routeIs('user-management')"
                    class="!text-white/90 hover:!text-white hover:bg-white/10 data-[current]:!bg-white/20 data-[current]:!text-white"
                    wire:navigate>
                    {{ __('User Management') }}
                </flux:navlist.item>

                <flux:navlist.item icon="academic-cap" :href="route('program-studi-management')"
                    :current="request()->routeIs('program-studi-management')"
                    class="!text-white/90 hover:!text-white hover:bg-white/10 data-[current]:!bg-white/20 data-[current]:!text-white"
                    wire:navigate>
                    {{ __('Study Program') }}
                </flux:navlist.item>

                <flux:navlist.item icon="rectangle-stack" :href="route('mata-kuliah-management')"
                    :current="request()->routeIs('mata-kuliah-management')"
                    class="!text-white/90 hover:!text-white hover:bg-white/10 data-[current]:!bg-white/20 data-[current]:!text-white"
                    wire:navigate>
                    {{ __('Mata Kuliah') }}
                </flux:navlist.item>

            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            {{-- <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item> --}}
        </flux:navlist>


<div x-data="{
    isAuto: $flux.appearance === 'system',
    updateAppearance(val) {
        $flux.appearance = val;
        this.isAuto = (val === 'system');
    },
    manualToggle() {
        this.isAuto = false;
        const nextTheme = $flux.appearance === 'dark' ? 'light' : 'dark';
        $flux.appearance = nextTheme;
    }
}" class="px-3 mb-4">
    <div class="flex items-center justify-between p-2.5 border rounded-xl border-white/20 bg-white/10 dark:bg-black/20 backdrop-blur-sm">

        {{-- Kiri: Checkbox Auto dengan Label Manual --}}
        <div class="flex items-center space-x-2">
            {{-- Kita hilangkan atribut label='' agar tidak bentrok --}}
            <flux:checkbox 
                x-model="isAuto" 
                class="!mb-0 [--base-color:white] [--accent:theme(colors.amber.400)]" 
                @change="isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')" 
            />
            
            {{-- Label Manual: Ini PASTI Putih karena menggunakan class Tailwind standar --}}
            <span class="text-sm font-medium text-white select-none cursor-pointer" @click="isAuto = !isAuto; isAuto ? updateAppearance('system') : updateAppearance(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')">
                Auto
            </span>
        </div>

        {{-- Kanan: Toggle Switch --}}
        <div :class="isAuto ? 'opacity-40' : 'opacity-100'" class="transition-opacity duration-300">
            <button type="button" @click="manualToggle()"
                class="relative inline-flex items-center h-6 rounded-full w-11 transition-all focus:outline-none shadow-lg border border-white/10"
                :class="$flux.appearance === 'dark' ? 'bg-amber-400' : 'bg-sky-600'">
                
                <span
                    class="flex items-center justify-center w-4 h-4 transition-transform transform bg-white rounded-full shadow-sm"
                    :class="$flux.appearance === 'dark' ? 'translate-x-6' : 'translate-x-1'">
                    
                    <flux:icon x-show="$flux.appearance !== 'dark'" name="sun" variant="mini"
                        class="w-2.5 h-2.5 text-amber-500" />
                    
                    <flux:icon x-show="$flux.appearance === 'dark'" name="moon" variant="mini"
                        class="w-2.5 h-2.5 text-sky-900" />
                </span>
            </button>
        </div>
    </div>
</div>

        <!-- Desktop User Menu -->
        <livewire:navigation.profile-dropdown />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <livewire:navigation.mobile-profile-dropdown />


    {{ $slot }}

    @fluxScripts
</body>

</html>
