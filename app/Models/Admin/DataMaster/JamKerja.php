<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'jam_masuk', 'jam_pulang_senin_kamis', 'jam_pulang_jumat'])]
#[Table('kepegawaian_master_jam_kerja')]
class JamKerja extends Model
{
    use HasFactory;
}
