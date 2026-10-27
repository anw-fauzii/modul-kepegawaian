<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\Bulan;
use Illuminate\Database\Eloquent\Collection;

class BulanService
{
    public function getAll(): Collection
    {
        return Bulan::with('tahunAjaran')->orderByDesc('id')->get();
    }

    public function store(array $data): Bulan
    {
        return Bulan::create($data);
    }

    public function update(Bulan $bulan, array $data): bool
    {
        return $bulan->update($data);
    }

    public function delete(Bulan $bulan): bool
    {
        return $bulan->delete();
    }
}
