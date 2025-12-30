<div>
    {{-- ****************************************************** --}}
    {{-- 1. UPLOAD EXCEL FILE --}}
    {{-- ***********************F******************************* --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Upload File Excel</h4>

        {{-- 📁 File Input --}}
        <div>
            <label for="excelFile" class="block text-sm font-medium text-gray-700">Pilih File
                Excel
                <span class="text-red-500">*</span></label>

            <div class="mt-1" wire:key="upload-container">
                <input wire:model="excelFile" type="file" id="excelFile" accept=".xlsx, .xls"
                    wire:key="excel-input-field"
                    class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            @error('excelFile')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- ****************************************************** --}}
    {{-- 2. TABEL INPUT HASIL PARSING --}}
    {{-- ****************************************************** --}}

    <div class="p-4 mt-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">

        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">
            Preview & Edit Data Pengguna
        </h4>

        @if (empty($parsedRows))
            <div class="text-sm text-gray-500 italic">
                Data dari Excel akan tampil di sini setelah file diunggah.
            </div>
        @else
            <div class="overflow-x-auto max-h-[55vh] overflow-y-auto border rounded-lg">

                <table class="min-w-full border-collapse text-sm">
                    <thead class="sticky top-0 bg-gray-100 z-10">
                        <tr class="text-left text-gray-700">
                            <th class="px-3 py-2 border whitespace-nowrap text-center">#</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Email</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Password</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Nama</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">NIP</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">NITK</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">NIDN</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">NIDK</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">NIM</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Thn Masuk</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Program Studi
                            </th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Role</th>
                            <th class="px-3 py-2 border whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($parsedRows as $i => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-1 border text-center font-semibold text-gray-600">
                                    {{ $i + 1 }}
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="email" wire:model.lazy="parsedRows.{{ $i }}.email"
                                        class="w-48 border rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.password"
                                        class="w-48 border rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-500 outline-none"
                                        placeholder="Default / custom">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.name"
                                        class="w-56 border rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-500 outline-none">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.nip"
                                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.nitk"
                                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.nidn"
                                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.nidk"
                                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text" wire:model.lazy="parsedRows.{{ $i }}.nim"
                                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td>

                                {{-- Tahun Masuk: Input dikecilkan --}}
                                <td class="px-2 py-1 border">
                                    <input type="number"
                                        wire:model.lazy="parsedRows.{{ $i }}.tahun_angkatan"
                                        class="w-full border rounded px-1 py-1 text-xs text-center appearance-none"
                                        inputmode="numeric" pattern="[0-9]*" maxlength="4"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                        placeholder="YYYY">
                                </td>

                                <td class="px-2 py-1 border">
                                    <input type="text"
                                        wire:model.lazy="parsedRows.{{ $i }}.program_studi"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td>

                                {{-- Role: Diberi styling Select yang lebih jelas --}}
                                <td class="px-2 py-1 border">
                                    <div class="relative">
                                        <select wire:model.lazy="parsedRows.{{ $i }}.role"
                                            class="w-24 border rounded pl-2 pr-4 py-1 text-xs bg-gray-50 cursor-pointer focus:bg-white appearance-none">
                                            <option value="admin">Admin</option>
                                            <option value="dosen">Dosen</option>
                                            <option value="mahasiswa">Mahasiswa</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-gray-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                            </svg>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-2 py-1 border text-center">
                                    <button wire:click="removeParsedRow({{ $i }})" type="button"
                                        class="text-red-500 hover:text-red-700 p-1 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            {{-- Error Baris --}}
                            @if (!empty($rowErrors[$i]))
                                <tr>
                                    <td colspan="13"
                                        class="px-4 py-1 bg-red-50 text-red-600 text-[10px] border italic">
                                        ⚠️ {{ $rowErrors[$i] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
