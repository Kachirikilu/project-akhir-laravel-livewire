<div class="flex space-x-4 overflow-x-auto pb-2 md:pb-0">
    {{-- Tab Semua --}}
    <button wire:click="filterBy('')"
        class="{{ isset($filter) && $filter == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                    tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
        <i class="fas fa-users mr-2"></i> Semua {{ ucfirst($typeOfXString) }} (<span id="count-all">{{ $totalTab }}</span>)
    </button>
    {{-- Tab 1 --}}
    <button wire:click="filterBy('{{ $tab1String }}')"
        class="{{ isset($filter) && $filter == $tab1String ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                    tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
        <i class="fas fa-crown mr-2"></i> {{ ucfirst($tab1String) }} (<span id="count-{{ $tab1String }}">{{ $totalTab1 }}</span>)
    </button>
    {{-- Tab 2 --}}
    <button wire:click="filterBy('{{ $tab2String }}')"
        class="{{ isset($filter) && $filter == $tab2String ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                    tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
        <i class="fas fa-chalkboard-teacher mr-2"></i> {{ ucfirst($tab2String) }} (<span id="count-{{ $tab2String }}">{{ $totalTab2 }}</span>)
    </button>
    {{-- Tab 3 --}}
    <button wire:click="filterBy('{{ $tab3String }}')"
        class="{{ isset($filter) && $filter == $tab3String ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                    tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
        <i class="fas fa-user-graduate mr-2"></i> {{ ucfirst($tab3String) }} (<span id="count-{{ $tab3String }}">{{ $totalTab3 }}</span>)
    </button>
</div>
