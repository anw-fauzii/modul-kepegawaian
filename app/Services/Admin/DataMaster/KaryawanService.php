<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
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

            return Karyawan::create(array_merge($data, ['nipy' => $user->no_induk]));
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

        $result = DB::transaction(function () use ($karyawan, $data) {
            unset($data['nipy']);

            $karyawan->user?->update(['name' => $data['nama_lengkap']]);

            return $karyawan->update($data);
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
