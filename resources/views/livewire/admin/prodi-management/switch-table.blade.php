<div class="mb-2 p-4 bg-white rounded-lg shadow-md border border-gray-100">

    <div class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="flex space-x-4 overflow-x-auto pb-2 md:pb-0">
            {{-- Program Studi --}}
            <button wire:click="switchingTable('prodi')"
                class="{{ isset($switchTable) && $switchTable == 'prodi' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-users mr-2"></i> Program Studi (<span id="count-all">{{ $totalProdis }}</span>)
            </button>
            {{-- Tab Jurusan --}}
            <button wire:click="switchingTable('jurusan')"
                class="{{ isset($switchTable) && $switchTable == 'jurusan' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-crown mr-2"></i> Jurusan (<span id="count-jurusan">{{ $totalJurusan }}</span>)
            </button>
            {{-- Tab Fakultas --}}
            <button wire:click="switchingTable('fakultas')"
                class="{{ isset($switchTable) && $switchTable == 'fakultas' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Fakultas (<span id="count-fakultas">{{ $totalFakultas }}</span>)
            </button>
        </div>

    </div>
</div>