<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\Admin\DataMaster\RiwayatJabatan;
use App\Models\User;
use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class KaryawanService
{
    public function getAll(): Collection
    {
        return Karyawan::with(['kategori', 'jenjangPendidikan', 'user'])->orderBy('nama_lengkap')->get();
    }

    public function getByUnit(string $unit): Collection
    {
        $categories = Kategori::all()->filter(function ($k) use ($unit) {
            return strcasecmp(SidebarComposer::extractUnitName($k->nama_kategori), $unit) === 0;
        });

        $kategoriIds = $categories->pluck('id')->all();

        return Karyawan::with(['kategori', 'jenjangPendidikan', 'user'])
            ->where(function ($query) use ($kategoriIds, $unit) {
                if (! empty($kategoriIds)) {
                    $query->whereIn('kepegawaian_master_kategori_id', $kategoriIds);
                }
            })
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function store(array $data): Karyawan
    {
        if (($data['foto'] ?? null) instanceof UploadedFile) {
            $data['foto'] = $data['foto']->store('karyawan/foto', 'private');
        } else {
            unset($data['foto']);
        }

        $karyawan = DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['nama_lengkap'],
                'no_induk' => $data['nipy'],
                'email'    => $data['email'],
                'password' => "Password",
            ]);

            $created = Karyawan::create(array_merge($data, ['nipy' => $user->no_induk]));

            if (! empty($created->jabatan)) {
                RiwayatJabatan::create([
                    'karyawan_nipy'                  => $created->nipy,
                    'kepegawaian_master_kategori_id' => $created->kepegawaian_master_kategori_id,
                    'jabatan'                        => $created->jabatan,
                    'unit'                           => $created->unit,
                    'tanggal_mulai'                  => $created->mulai_tugas ?? now()->toDateString(),
                    'tanggal_selesai'                => null,
                    'is_aktif'                       => true,
                    'keterangan'                     => 'Penetapan jabatan awal pegawai',
                ]);
            }

            return $created;
        });

        Cache::forget(SidebarComposer::CACHE_KEY);

        return $karyawan;
    }

    public function update(Karyawan $karyawan, array $data): bool
    {
        $oldFoto = null;
        if (($data['foto'] ?? null) instanceof UploadedFile) {
            $oldFoto = $karyawan->foto;
            $data['foto'] = $data['foto']->store('karyawan/foto', 'private');
        } else {
            unset($data['foto']);
        }

        $oldJabatan = trim((string) ($karyawan->jabatan ?? ''));
        $oldUnit = trim((string) ($karyawan->unit ?? ''));
        $oldKategoriId = (int) ($karyawan->kepegawaian_master_kategori_id ?? 0);

        $result = DB::transaction(function () use ($karyawan, $data, $oldJabatan, $oldUnit, $oldKategoriId) {
            unset($data['nipy']);

            $karyawan->user?->update(['name' => $data['nama_lengkap']]);

            $updated = $karyawan->update($data);

            $newJabatan = trim((string) ($data['jabatan'] ?? $karyawan->jabatan ?? ''));
            $newUnit = trim((string) ($data['unit'] ?? $karyawan->unit ?? ''));
            $newKategoriId = (int) ($data['kepegawaian_master_kategori_id'] ?? $karyawan->kepegawaian_master_kategori_id ?? 0);

            $hasPositionChanged = $newJabatan !== '' && (
                strcasecmp($oldJabatan, $newJabatan) !== 0
                || strcasecmp($oldUnit, $newUnit) !== 0
                || $oldKategoriId !== $newKategoriId
            );

            if ($hasPositionChanged) {
                $existingCount = RiwayatJabatan::where('karyawan_nipy', $karyawan->nipy)->count();

                // Jika belum ada histori sama sekali tetapi sebelumnya punya jabatan, arsipkan jabatan lama terlebih dahulu
                if ($existingCount === 0 && $oldJabatan !== '') {
                    RiwayatJabatan::create([
                        'karyawan_nipy'                  => $karyawan->nipy,
                        'kepegawaian_master_kategori_id' => $oldKategoriId ?: null,
                        'jabatan'                        => $oldJabatan,
                        'unit'                           => $oldUnit ?: null,
                        'tanggal_mulai'                  => $karyawan->mulai_tugas,
                        'tanggal_selesai'                => now()->toDateString(),
                        'is_aktif'                       => false,
                        'keterangan'                     => 'Jabatan sebelumnya',
                    ]);
                } else {
                    RiwayatJabatan::where('karyawan_nipy', $karyawan->nipy)
                        ->where('is_aktif', true)
                        ->update([
                            'is_aktif'        => false,
                            'tanggal_selesai' => DB::raw('COALESCE(tanggal_selesai, CURRENT_DATE)'),
                        ]);
                }

                RiwayatJabatan::create([
                    'karyawan_nipy'                  => $karyawan->nipy,
                    'kepegawaian_master_kategori_id' => $newKategoriId ?: null,
                    'jabatan'                        => $newJabatan,
                    'unit'                           => $newUnit ?: null,
                    'tanggal_mulai'                  => now()->toDateString(),
                    'tanggal_selesai'                => null,
                    'is_aktif'                       => true,
                    'keterangan'                     => 'Perubahan data jabatan/unit/kategori pegawai',
                ]);
            }

            return $updated;
        });

        if ($oldFoto) {
            if (Storage::disk('private')->exists($oldFoto)) {
                Storage::disk('private')->delete($oldFoto);
            }
            if (Storage::disk('public')->exists($oldFoto)) {
                Storage::disk('public')->delete($oldFoto);
            }
        }

        Cache::forget(SidebarComposer::CACHE_KEY);

        return $result;
    }

    public function delete(Karyawan $karyawan): bool
    {
        $result = DB::transaction(function () use ($karyawan) {
            $user = $karyawan->user;
            $deleted = $karyawan->delete();
            $user?->delete();

            return $deleted;
        });

        if ($karyawan->foto) {
            if (Storage::disk('private')->exists($karyawan->foto)) {
                Storage::disk('private')->delete($karyawan->foto);
            }
            if (Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }
        }

        Cache::forget(SidebarComposer::CACHE_KEY);

        return $result;
    }
}
