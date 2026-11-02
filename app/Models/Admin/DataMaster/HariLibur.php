<?php

namespace App\Models\Admin\DataMaster;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tanggal', 'nama', 'keterangan', 'is_libur_nasional', 'is_cuti', 'jenis'])]
#[Table('kepegawaian_master_hari_libur')]
class HariLibur extends Model
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
            'tanggal'            => 'date',
            'is_libur_nasional'  => 'boolean',
            'is_cuti'            => 'boolean',
        ];
    }

    /**
     * Dapatkan mapping tanggal libur dalam format ['YYYY-MM-DD' => 'Nama Libur'].
     */
    public static function getHolidayMap(): array
    {
        return self::query()
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(function ($item) {
                $tgl = $item->tanggal instanceof Carbon ? $item->tanggal->format('Y-m-d') : substr((string) $item->tanggal, 0, 10);
                return [$tgl => $item->nama];
            })
            ->toArray();
    }

    /**
     * Periksa apakah suatu tanggal merupakan hari libur nasional / libur yayasan.
     */
    public static function isHoliday(string|Carbon $date): bool
    {
        $tglStr = $date instanceof Carbon ? $date->format('Y-m-d') : substr((string) $date, 0, 10);
        return self::whereDate('tanggal', $tglStr)->exists();
    }
}
