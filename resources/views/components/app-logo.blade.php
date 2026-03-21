{{-- <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
</div> --}}

{{-- <div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">Universitas Sriwijaya</span>
</div> --}}

<div class="flex items-center gap-3">
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-transparent">
        <x-app-logo-icon class="w-8 h-auto" />
    </div>

    <div class="grid flex-1 text-start text-sm transition-colors duration-200">
        <span class="truncate leading-tight font-bold text-base text-white dark:text-gray-200" x-show="expanded" x-cloak
        x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
            Universitas Sriwijaya
        </span>
        <span class="truncate text-xs text-gray-400 dark:text-gray-500 font-medium" x-show="expanded" x-cloak
        x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
            Sistem Informasi Akademik
        </span>
    </div>
</div>
