<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\JamKerja;
use Illuminate\Database\Eloquent\Collection;

class JamKerjaService
{
    public function getAll(): Collection
    {
        return JamKerja::orderBy('nama')->get();
    }

    public function store(array $data): JamKerja
    {
        return JamKerja::create($data);
    }

    public function update(JamKerja $jamKerja, array $data): bool
    {
        return $jamKerja->update($data);
    }

    public function delete(JamKerja $jamKerja): bool
    {
        return $jamKerja->delete();
    }
}
