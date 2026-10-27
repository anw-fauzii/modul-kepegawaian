<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_tahun_ajaran', 'semester', 'is_active'])]
#[Table('akademik_master_tahun_ajaran')]
class TahunAjaran extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function bulan(): HasMany
    {
        return $this->hasMany(Bulan::class, 'akademik_master_tahun_ajaran_id');
    }

    public function kegiatanLembaga(): HasMany
    {
        return $this->hasMany(\App\Models\Admin\Kegiatan\KegiatanLembaga::class, 'akademik_master_tahun_ajaran_id');
    }

    public function suratKeputusan(): HasMany
    {
        return $this->hasMany(\App\Models\Admin\SuratKeputusan\SuratKeputusan::class, 'akademik_master_tahun_ajaran_id');
    }

    public function rapotKinerja(): HasMany
    {
        return $this->hasMany(\App\Models\Admin\RapotKinerja\RapotKinerja::class, 'akademik_master_tahun_ajaran_id');
    }
}

