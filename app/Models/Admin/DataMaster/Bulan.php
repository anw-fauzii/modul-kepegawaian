<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Admin\Presensi\Presensi;

#[Fillable(['nama_bulan', 'akademik_master_tahun_ajaran_id', 'status'])]
#[Table('kepegawaian_master_bulan')]
class Bulan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'akademik_master_tahun_ajaran_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'kepegawaian_master_bulan_id');
    }
}
