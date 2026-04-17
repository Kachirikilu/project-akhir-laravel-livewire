<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    <flux:toast />
    @fluxScripts
</x-layouts.app.sidebar>
