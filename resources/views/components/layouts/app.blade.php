<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
        <flux:toast />
        @fluxScripts
    </flux:main>
</x-layouts.app.sidebar>
