<flux:dropdown class="block" align="start">

    {{-- @if (auth()->user()->profile_photo_path)
        <flux:profile 
            name="{{ auth()->user()->name }}" 
            avatar="{{ auth()->user()->profile_photo_url }}" 
            icon:trailing="chevron-up" 
            class="[&_span]:!text-white [&_svg]:!text-white"
        />
    @else
        <flux:profile 
            name="{{ auth()->user()->name }}" 
            initials="{{ $userInitials }}" 
            icon:trailing="chevron-up" 
            class="[&_span]:!text-white [&_svg]:!text-white"
        />
    @endif --}}

    <!-- Trigger -->
    <div>

        <button @click="open = !open" @click.outside="open = false"
            class="cursor-pointer w-full flex items-center gap-4 p-1 rounded-lg hover:bg-white/10 transition">
            <!-- Avatar -->
            @if (auth()->user()->profile_photo_path)
                <img src="{{ auth()->user()->profile_photo_url }}" alt="avatar" class="w-8 h-8 rounded-lg object-cover">
            @else
                <div
                    class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-xs font-semibold text-white">
                    {{ $userInitials }}
                </div>
            @endif

            <!-- Name -->
            <span x-show="expanded" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-200 ease-in"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
                class="text-white text-sm whitespace-nowrap overflow-hidden text-ellipsis block">
                {{ auth()->user()->name }} qwd qwd wd qwd qwdqwdd
            </span>

            <!-- Chevron -->
            <svg class="w-8 h-8 text-white transition-transform pl-2" :class="{ 'rotate-180': open }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

    </div>

    <flux:menu class="w-[224px] dark:!bg-gray-800">
        <flux:menu.radio.group>
            <div class="p-0 text-sm font-normal">
                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                                class="h-full w-full object-cover">
                        @else
                            <span
                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 bg-[var(--contrast-main-color)] text-[var(--contrast-main-text)]">
                                {{ $userInitials }}
                            </span>
                        @endif
                    </span>

                    <div class="grid flex-1 text-start text-sm leading-tight">
                        <span class="truncate font-semibold text-[var(--contrast-main-text)]">{{ $userName }}</span>
                        <span class="truncate text-xs text-[var(--contrast-second-text)]">{{ $userEmail }}</span>
                    </div>
                </div>
            </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                data-test="logout-button">
                {{ __('Log Out') }}
            </flux:menu.item>
        </form>
    </flux:menu>

</flux:dropdown>
