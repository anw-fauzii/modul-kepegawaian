<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\HariLibur;
use Illuminate\Database\Eloquent\Collection;

class HariLiburService
{
    public function getAll(): Collection
    {
        return HariLibur::orderByDesc('tanggal')->get();
    }

    public function getByYear(?int $year = null): Collection
    {
        $year = $year ?: now()->year;
        return HariLibur::whereYear('tanggal', $year)->orderBy('tanggal')->get();
    }

    public function store(array $data): HariLibur
    {
        return HariLibur::create($data);
    }

    public function update(HariLibur $hariLibur, array $data): bool
    {
        return $hariLibur->update($data);
    }

    public function delete(HariLibur $hariLibur): bool
    {
        return $hariLibur->delete();
    }
}
