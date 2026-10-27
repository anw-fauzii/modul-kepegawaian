<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\Kategori;
use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class KategoriService
{
    public function getAll(): Collection
    {
        return Kategori::orderBy('nama_kategori')->get();
    }

    public function store(array $data): Kategori
    {
        $kategori = Kategori::create($data);
        Cache::forget(SidebarComposer::CACHE_KEY);

        return $kategori;
    }

    public function update(Kategori $kategori, array $data): bool
    {
        $result = $kategori->update($data);
        Cache::forget(SidebarComposer::CACHE_KEY);

        return $result;
    }

    public function delete(Kategori $kategori): bool
    {
        $result = $kategori->delete();
        Cache::forget(SidebarComposer::CACHE_KEY);

        return $result;
    }
}