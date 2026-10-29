<?php

namespace App\Imports;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Penggajian\Penggajian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import slip gaji dari Excel. Baris di-upsert berdasarkan NIPY + bulan.
 * Slip berstatus "dibayar" dilewati (terkunci).
 */
class PenggajianImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;
    public int $updated = 0;
    public array $errors = [];

    /**
     * Pemetaan nama kolom di Excel ke nama field model Penggajian.
     */
    protected const FIELD_ALIASES = [
        'gaji_pokok'        => ['gaji_pokok', 'gajipokok', 'gaji_pokok_rp'],
        'tpp'               => ['tpp'],
        'pembulatan'        => ['pembulatan'],
        't_struktural'      => ['t_struktural', 'tunj_struktural', 'tunjangan_struktural', 'struktural'],
        't_fungsional'      => ['t_fungsional', 'tunj_fungsional', 'tunjangan_fungsional', 'fungsional'],
        't_amanah_tambahan' => ['t_amanah_tambahan', 'tunj_amanah_tambahan', 'tunjangan_amanah_tambahan', 'amanah_tambahan'],
        't_transport'       => ['t_transport', 'tunj_transport', 'tunjangan_transport', 'transport'],
        't_makan'           => ['t_makan', 'tunj_makan', 'tunjangan_makan', 'makan'],
        't_kesehatan'       => ['t_kesehatan', 'tunj_kesehatan', 'tunjangan_kesehatan', 'kesehatan'],
        't_keluarga'        => ['t_keluarga', 'tunj_keluarga', 'tunjangan_keluarga', 'keluarga'],
        't_pendidikan_anak' => ['t_pendidikan_anak', 'tunj_pendidikan_anak', 'tunjangan_pendidikan_anak', 'pendidikan_anak'],
        't_kuota'           => ['t_kuota', 'tunj_kuota', 'tunjangan_kuota', 'kuota'],
        'lain_lain'         => ['lain_lain', 'lainlain', 'tunjangan_lain_lain'],
        'reward_kehadiran'  => ['reward_kehadiran', 'rewardkehadiran', 'reward'],
        'kekurangan_gaji'   => ['kekurangan_gaji', 'kekurangan_gaji_bulan_lalu', 'kurang_gaji'],
        // Potongan
        'pot_makan'         => ['pot_makan', 'potongan_makan', 'potong_makan'],
        'pot_kehadiran'     => ['pot_kehadiran', 'potongan_kehadiran', 'potong_kehadiran'],
        'pot_pendidikan'    => ['pot_pendidikan', 'potongan_pendidikan', 'potong_pendidikan'],
        'pot_kesehatan'     => ['pot_kesehatan', 'potongan_kesehatan', 'potong_kesehatan'],
        'infaq'             => ['infaq', 'infak'],
        'adm_sekolah'       => ['adm_sekolah', 'administrasi_sekolah', 'adm'],
        'kelebihan_gaji'    => ['kelebihan_gaji', 'kelebihan_gaji_bulan_lalu', 'lebih_gaji'],
    ];

    public function __construct(
        protected int $bulanId,
        protected string $status = 'draft',
        protected string $jenis = 'gaji'
    ) {}

    public function collection(Collection $rows): void
    {
        $fields = array_merge(array_keys(Penggajian::PENERIMAAN), array_keys(Penggajian::POTONGAN));

        foreach ($rows as $index => $row) {
            $line = $index + 2; // baris 1 = heading

            $nipy = trim((string) ($row['nipy'] ?? $row['nip'] ?? $row['nipy_karyawan'] ?? ''));

            if ($nipy === '') {
                continue;
            }

            $karyawan = Karyawan::where('nipy', $nipy)->first();
            if (! $karyawan) {
                $this->errors[] = "Baris {$line}: NIPY {$nipy} tidak ditemukan.";
                continue;
            }

            $slip = Penggajian::firstOrNew([
                'karyawan_nipy'               => $nipy,
                'kepegawaian_master_bulan_id' => $this->bulanId,
                'jenis'                       => $this->jenis,
            ]);

            if ($slip->exists && $slip->status === 'dibayar') {
                $this->errors[] = "Baris {$line}: slip {$nipy} sudah dibayar (terkunci), dilewati.";
                continue;
            }

            $data = [
                'unit'     => $karyawan->unit,
                'jabatan'  => $karyawan->jabatan,
                'golongan' => $karyawan->golongan,
                'jenis'    => $this->jenis,
            ];

            foreach ($fields as $field) {
                $val = 0;
                $aliases = self::FIELD_ALIASES[$field] ?? [$field];
                foreach ($aliases as $alias) {
                    if (isset($row[$alias]) && $row[$alias] !== '') {
                        $val = $row[$alias];
                        break;
                    }
                }
                $data[$field] = $this->angka($val);
            }

            $isNew = ! $slip->exists;
            if ($isNew) {
                $data['status'] = $this->status;
            }

            $slip->fill($data)->save();
            $isNew ? $this->created++ : $this->updated++;
        }
    }

    private function angka(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        // Format "1.500.000,50" atau "Rp 1.500.000"
        $clean = preg_replace('/[^\d,.-]/', '', (string) $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return is_numeric($clean) ? (float) $clean : 0;
    }
}
