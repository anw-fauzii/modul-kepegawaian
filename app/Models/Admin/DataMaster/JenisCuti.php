<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'lama_hari', 'keterangan', 'is_active'])]
#[Table('kepegawaian_master_jenis_cuti')]
class JenisCuti extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lama_hari' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function cuti(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Admin\Cuti\Cuti::class, 'kepegawaian_master_jenis_cuti_id');
    }
}
