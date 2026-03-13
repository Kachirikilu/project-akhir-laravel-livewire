<?php

namespace App\Livewire\Admin\ProdiManagement;

// use App\Models\User;
// use App\Models\Admin;
// use App\Models\Dosen;
// use App\Models\Mahasiswa;
use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// use Livewire\WithFileUploads;
// use Livewire\Attributes\Validate;

// use Illuminate\Support\LazyCollection;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;

trait WithProdiModal
{
    // use WithFileUploads;

    public $selected_id;

    public $showProdiModal = false;

    public $isEditing = false;

    public $prodiType;

    public $prodi_id;

    public $jurusan_id;

    public $fakultas_id;

    public $nama_prodi;

    public $nama_strata;

    public $nama_jurusan;

    public $nama_fakultas;

    public $prodi_name_search;

    public $prodi_results;

    public $selectedProdiId;

    public $selectedJurusanId;

    public $selectedFakultasId;

    // public $excelFile;
    // public array $parsedRows = [];
    // public array $rowErrors  = [];

    protected $prodis = [
        'prodi_id' => 'required|exists:prodis,id',
        'nama_prodi' => 'required|string|max:255|unique:prodis,nama_prodi',
        'nama_fakultas' => 'required|string|max:255|unique:fakultas,nama_fakultas',
        'jurusan_id' => 'required|exists:jurusans,id',
        'nama_jurusan' => 'required|string|max:255|unique:jurusans,nama_jurusan',
        'fakultas_id' => 'required|exists:fakultas,id',
        'nama_strata' => 'required|string|max:255',
    ];

    // public function resetModalFields()
    // {
    //     $this->reset([
    //         'userId', 'email', 'password', 'name', 'nip', 'nim',
    //         'tahun_angkatan', 'prodi_id', 'prodiType', 'isEditing',
    //         'prodi_name_search', 'showProdiModal'
    //     ]);

    //     $this->resetValidation();
    // }

    public function addProdi($prodi)
    {
        if ($this->isEditing) {
            $this->resetInput();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->prodiType = $prodi;
        $this->showProdiModal = true;
        // $this->js("Flux.modal('user-modal').show()");
        if ($prodi === 'prodi') {
            // $this->inputProdiFilter();
            $this->updatedJurusanNameSearch($this->jurusan_name_search);
        } elseif ($prodi === 'jurusan') {
            // $this->inputJurusanFilter();
            $this->updatedFakultasNameSearch($this->fakultas_name_search);
        }
        // $this->updatedProdiNameSearch($this->prodi_name_search);
    }

    public function editProdi($id, $type)
    {
        $this->selected_id = $id;
        $this->prodiType = $type;
        $this->isEditing = true;

        $this->resetValidation();
        $this->resetErrorBag();

        $this->nama_prodi = $this->nama_jurusan = $this->nama_fakultas = $this->nama_strata = null;
        $this->jurusan_id = $this->fakultas_id = $this->selected_id = null;

        try {
            if ($type === 'prodi') {
                $prodi = Prodi::with('jurusan_rel')->findOrFail($id);
                $this->selected_id = $prodi->id;
                $this->nama_prodi = $prodi->nama_prodi;
                $this->nama_strata = $prodi->nama_strata;
                $this->jurusan_id = $prodi->jurusan_id;
                $this->jurusan_name_search = $prodi->jurusan_rel->nama_jurusan ?? '';

                if ($this->jurusan_id) {
                    $jurusan = Jurusan::find($this->jurusan_id);
                    $this->jurusan_name_search = $jurusan ? 'Jurusan ' . $jurusan->nama_jurusan : '';
                } else {
                    $this->jurusan_name_search = '';
                }
                $this->getJurusanbyUser();
                $this->fetchJurusan($this->jurusan_name_search);

            } elseif ($type === 'jurusan') {
                $jurusan = Jurusan::with('fakultas_rel')->findOrFail($id);
                $this->selected_id = $jurusan->id;
                $this->nama_jurusan = $jurusan->nama_jurusan;
                $this->fakultas_id = $jurusan->fakultas_id;
                $this->fakultas_name_search = $jurusan->fakultas_rel->nama_fakultas ?? '';

                if ($this->fakultas_id) {
                    $fakultas = Fakultas::find($this->fakultas_id);
                    $this->fakultas_name_search = $fakultas ? 'Fakultas ' .  $fakultas->nama_fakultas : '';
                } else {
                    $this->fakultas_name_search = '';
                }
                $this->getFakultasbyUser();
                $this->fetchFakultas($this->fakultas_name_search);

            } elseif ($type === 'fakultas') {
                $fakultas = Fakultas::findOrFail($id);
                $this->selected_id = $fakultas->id;
                $this->nama_fakultas = $fakultas->nama_fakultas;
            }

            $this->showProdiModal = true;
            // $this->js("Flux.modal('prodi-modal').show()");

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Data tidak ditemukan!');
        }
    }

    public function inputModalProdi($isEditing, $data)
    {
        $prodis = [];

        /* ===================== PROGRAM STUDI ===================== */
        if ($this->prodiType === 'prodi') {
            $prodis = [
                'nama_prodi' => [
                    'required',
                    'string',
                    'max:255',
                    // Menggunakan uniqueRule dengan ID saat editing agar tidak error jika nama tetap sama
                    $this->uniqueRule('prodis', 'nama_prodi', $isEditing ? $this->selected_id : null),
                ],
                'nama_strata' => ['required', 'string', 'max:255'],
                'jurusan_id' => ['required', 'exists:jurusans,id'],
            ];
        }

        /* ===================== JURUSAN ===================== */
        elseif ($this->prodiType === 'jurusan') {
            $prodis = [
                'nama_jurusan' => [
                    'required',
                    'string',
                    'max:255',
                    $this->uniqueRule('jurusans', 'nama_jurusan', $isEditing ? $this->selected_id : null),
                ],
                'fakultas_id' => ['required', 'exists:fakultas,id'],
            ];
        }

        /* ===================== FAKULTAS ===================== */
        elseif ($this->prodiType === 'fakultas') {
            $prodis = [
                'nama_fakultas' => [
                    'required',
                    'string',
                    'max:255',
                    $this->uniqueRule('fakultas', 'nama_fakultas', $isEditing ? $this->selected_id : null),
                ],
            ];
        }

        return Validator::make($data, $prodis, $this->validationMessages())->validate();
    }

    private function uniqueRule(string $table, string $column, $id = null)
    {
        return $id ? Rule::unique($table, $column)->ignore($id) : Rule::unique($table, $column);
    }

    public function saveProdi($data)
    {
        $validated = $this->inputModalProdi(false, $data);

        if ($this->prodiType === 'prodi') {
            Prodi::create([
                'nama_prodi' => $validated['nama_prodi'] ?? $this->nama_prodi,
                'nama_strata' => $validated['nama_strata'] ?? $this->nama_strata,
                'jurusan_id' => $validated['jurusan_id'] ?? $this->jurusan_id,
            ]);
        } elseif ($this->prodiType === 'jurusan') {
            Jurusan::create([
                'nama_jurusan' => $validated['nama_jurusan'] ?? $this->nama_jurusan,
                'fakultas_id' => $validated['fakultas_id'] ?? $this->fakultas_id,
            ]);
        } elseif ($this->prodiType === 'fakultas') {
            Fakultas::create([
                'nama_fakultas' => $validated['nama_fakultas'] ?? $this->nama_fakultas,
            ]);
        }

        $this->resetInput();
        $this->showProdiModal = false;
        $this->dispatch('toast', message: '✅ Data berhasil ditambahkan.');
    }

    public function updateProdi($data)
    {
        $validated = $this->inputModalProdi(true, $data);

        try {
            if ($this->prodiType === 'prodi') {
                Prodi::findOrFail($this->selected_id)->update([
                    'nama_prodi' => $validated['nama_prodi'] ??  $this->nama_prodi,
                    'nama_strata' => $validated['nama_strata'] ?? $this->nama_strata,
                    'jurusan_id' => $validated['jurusan_id'] ?? $this->jurusan_id,
                ]);
                $message = 'Program Studi';
            } elseif ($this->prodiType === 'jurusan') {
                Jurusan::findOrFail($this->selected_id)->update([
                    'nama_jurusan' => $validated['nama_jurusan'] ?? $this->nama_jurusan,
                    'fakultas_id' => $validated['fakultas_id'] ?? $this->fakultas_id,
                ]);
                $message = 'Jurusan';
            } elseif ($this->prodiType === 'fakultas') {
                Fakultas::findOrFail($this->selected_id)->update([
                    'nama_fakultas' => $validated['nama_fakultas'] ?? $this->nama_fakultas,
                ]);
                $message = 'Fakultas';
            }

            $this->showProdiModal = false;
            $this->dispatch('toast', message: "✅ Data $message berhasil diperbarui.");

            $this->dispatch('refresh-data');

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function validationMessages()
    {
        return [
            'nama_prodi.required' => 'Nama program studi wajib diisi!',
            'nama_prodi.max' => 'Nama program studi tidak boleh lebih dari 255 karakter!',
            'nama_prodi.unique' => 'Nama program studi sudah ada di database!',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi!',
            'nama_jurusan.max' => 'Nama jurusan tidak boleh lebih dari 255 karakter!',
            'nama_jurusan.unique' => 'Nama jurusan sudah ada di database!',
            'nama_fakultas.required' => 'Nama fakultas wajib diisi!',
            'nama_fakultas.max' => 'Nama fakultas tidak boleh lebih dari 255 karakter!',
            'nama_fakultas.unique' => 'Nama fakultas sudah ada di database!',
            'nama_strata.required' => 'Nama strata wajib diisi!',
            'jurusan_id.required' => 'Jurusan wajib diisi!',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid!',
            'fakultas_id.required' => 'Fakultas wajib diisi!',
            'fakultas_id.exists' => 'Fakultas yang dipilih tidak valid!'
        ];
    }

    public function resetInput($keepProdi = false)
    {
        $fields = [
            'nama_prodi', 'nama_strata', 'nama_jurusan', 'nama_fakultas',
            'jurusan_id', 'fakultas_id', 'jurusan_name_search', 'fakultas_name_search',
        ];

        if (! $keepProdi) {
            $fields = array_merge($fields, ['prodi_id', 'prodi_name_search', 'prodi_results']);
        }

        $this->selected_id = null;
        $this->reset($fields);
        $this->resetErrorBag();
    }
}
