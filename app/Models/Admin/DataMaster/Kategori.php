<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kategori'])]
#[Table('kepegawaian_master_kategori')]
class Kategori extends Model
{
    use HasFactory;

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'kepegawaian_master_kategori_id');
    }
}