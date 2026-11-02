<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\JenisCuti;
use Illuminate\Database\Eloquent\Collection;

class JenisCutiService
{
    public function getAll(): Collection
    {
        return JenisCuti::orderBy('nama')->get();
    }

    public function getActive(): Collection
    {
        return JenisCuti::where('is_active', true)->orderBy('nama')->get();
    }

    public function store(array $data): JenisCuti
    {
        return JenisCuti::create($data);
    }

    public function update(JenisCuti $jenisCuti, array $data): bool
    {
        return $jenisCuti->update($data);
    }

    public function delete(JenisCuti $jenisCuti): bool
    {
        return $jenisCuti->delete();
    }
}
