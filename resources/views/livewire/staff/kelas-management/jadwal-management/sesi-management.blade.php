<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.staff.kelas-management.jadwal-management.jadwal-header', [
        'backUrl' => route('jadwal-management', ['kode' => $kelas->kode]),
        'mainKode' => $jadwal->kode ?? '-',
        'subLabel' => 'Kelas ' . ($jadwal->label_extra ?? '- ---'),
        'mainHead' => 'Jadwal Kelas',
        'subHead' => 'Sesi Kelas'
    ])

    @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-table')

    @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-modal-form')
    @include('livewire.staff.obe-management.rps-management.rps-show-modal', ['alpineKey' => 'sesi?.rps_id_show', 'isEdit' => 0])

</div>

