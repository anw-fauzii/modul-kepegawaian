<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\User;
use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
                $query->orWhere('unit', 'LIKE', '%' . $unit . '%');
            })
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function store(array $data): Karyawan
    {
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
        $result = DB::transaction(function () use ($karyawan, $data) {
            unset($data['nipy']);

            $karyawan->user?->update(['name' => $data['nama_lengkap']]);

            return $karyawan->update($data);
        });

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

        Cache::forget(SidebarComposer::CACHE_KEY);

        return $result;
    }
}
