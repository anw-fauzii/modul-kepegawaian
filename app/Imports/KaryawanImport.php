<?php

namespace App\Imports;

use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\JenjangPendidikan;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\User;
use App\View\Composers\SidebarComposer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import data karyawan dari Excel.
 * Mendukung insert baru & update (upsert) berdasarkan NIPY.
 */
class KaryawanImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;
    public int $updated = 0;
    public array $errors = [];

    public function __construct(
        protected bool $defaultActive = true
    ) {}

    public function collection(Collection $rows): void
    {
        // Cache data master untuk lookup cepat
        $kategoriList = Kategori::all();
        $jamKerjaList = JamKerja::all();
        $jenjangList  = JenjangPendidikan::all();

        $defaultKategoriId = $kategoriList->first()?->id ?? 1;
        $defaultJamKerjaId = $jamKerjaList->first()?->id ?? 1;
        $defaultJenjangId  = $jenjangList->first()?->id ?? 1;

        foreach ($rows as $index => $row) {
            $line = $index + 2; // Baris 1 adalah heading

            $nipy = trim((string) ($row['nipy'] ?? $row['nip'] ?? $row['no_induk'] ?? ''));
            $nama = trim((string) ($row['nama_lengkap'] ?? $row['nama'] ?? ''));

            // Lewati baris kosong
            if ($nipy === '' && $nama === '') {
                continue;
            }

            // Lewati baris contoh template jika tidak sengaja terupload
            if (str_contains(strtolower($nama), '(contoh)') || strtolower($nipy) === 'contoh') {
                continue;
            }

            if ($nipy === '') {
                $this->errors[] = "Baris {$line}: NIPY tidak boleh kosong.";
                continue;
            }

            if ($nama === '') {
                $this->errors[] = "Baris {$line}: Nama lengkap karyawan ({$nipy}) tidak boleh kosong.";
                continue;
            }

            // Lookup Kategori (berdasarkan ID atau nama kategori)
            $katInput   = trim((string) ($row['kategori'] ?? $row['kategori_karyawan'] ?? $row['kepegawaian_master_kategori_id'] ?? ''));
            $kategoriId = $this->resolveLookup($kategoriList, $katInput, 'nama_kategori', $defaultKategoriId);

            // Lookup Jam Kerja (berdasarkan ID atau nama)
            $jamInput   = trim((string) ($row['jam_kerja'] ?? $row['jamkerja'] ?? $row['kepegawaian_master_jam_kerja_id'] ?? ''));
            $jamKerjaId = $this->resolveLookup($jamKerjaList, $jamInput, 'nama', $defaultJamKerjaId);

            // Lookup Jenjang Pendidikan (berdasarkan ID atau nama_jenjang_pendidikan)
            $jenjangInput = trim((string) ($row['jenjang_pendidikan'] ?? $row['jenjang'] ?? $row['pendidikan'] ?? $row['akademik_pelengkap_jenjang_pendidikan_id'] ?? ''));
            $jenjangId    = $this->resolveLookup($jenjangList, $jenjangInput, 'nama_jenjang_pendidikan', $defaultJenjangId);

            // Parsing Jenis Kelamin
            $jkRaw = strtoupper(trim((string) ($row['jk'] ?? $row['jenis_kelamin'] ?? '')));
            $jk    = in_array($jkRaw, ['L', 'LAKI-LAKI', 'PRIA', 'M']) ? 'L' : (in_array($jkRaw, ['P', 'PEREMPUAN', 'WANITA', 'F']) ? 'P' : 'L');

            // Parsing Tanggal
            $tglLahir   = $this->parseDate($row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? null, '1990-01-01');
            $mulaiTugas = $this->parseDate($row['mulai_tugas'] ?? $row['tgl_mulai_tugas'] ?? null, date('Y-m-d'));

            // Parsing Sertifikasi
            $sertifikasiRaw = strtolower(trim((string) ($row['sertifikasi'] ?? '')));
            $sertifikasi    = in_array($sertifikasiRaw, ['1', 'true', 'ya', 'yes', 'sudah']);

            // Parsing Status Akun User
            $statusRaw = strtolower(trim((string) ($row['status_akun'] ?? $row['status'] ?? $row['is_active'] ?? '')));
            if ($statusRaw !== '') {
                $isActive = in_array($statusRaw, ['1', 'true', 'aktif', 'active', 'ya']);
            } else {
                $isActive = $this->defaultActive;
            }

            // Normalisasi Email
            $email = trim((string) ($row['email'] ?? ''));
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $cleanNipy = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nipy));
                $email     = ($cleanNipy ?: 'karyawan_' . $line) . '@primainsani.sch.id';
            }

            // Parsing data lainnya
            $nik         = trim((string) ($row['nik'] ?? '')) ?: null;
            $gelar       = trim((string) ($row['gelar'] ?? '')) ?: null;
            $tempatLahir = trim((string) ($row['tempat_lahir'] ?? '')) ?: 'Garut';
            $alamat      = trim((string) ($row['alamat'] ?? '')) ?: '-';
            $rt          = substr(trim((string) ($row['rt'] ?? '00')) ?: '00', 0, 5);
            $rw          = substr(trim((string) ($row['rw'] ?? '00')) ?: '00', 0, 5);
            $kelurahan   = trim((string) ($row['kelurahan'] ?? $row['desa'] ?? '')) ?: '-';
            $kecamatan   = trim((string) ($row['kecamatan'] ?? '')) ?: '-';
            $kabupaten   = trim((string) ($row['kabupaten'] ?? $row['kota'] ?? '')) ?: '-';
            $unit        = trim((string) ($row['unit'] ?? '')) ?: 'Yayasan Prima Insani';
            $jabatan     = trim((string) ($row['jabatan'] ?? '')) ?: 'Staff';
            $golongan    = trim((string) ($row['golongan'] ?? '')) ?: null;
            $perguruan   = trim((string) ($row['perguruan'] ?? $row['perguruan_tinggi'] ?? $row['universitas'] ?? $row['kampus'] ?? '')) ?: null;
            $prodi       = trim((string) ($row['prodi'] ?? $row['jurusan'] ?? '')) ?: null;
            $tahunLulus  = substr(trim((string) ($row['tahun_lulus'] ?? date('Y'))) ?: date('Y'), 0, 4);
            $whatsapp    = trim((string) ($row['whatsapp'] ?? $row['no_wa'] ?? '')) ?: null;
            $nuptk       = trim((string) ($row['nuptk'] ?? '')) ?: null;

            try {
                DB::transaction(function () use (
                    $nipy, $nama, $email, $isActive,
                    $nik, $gelar, $jk, $tempatLahir, $tglLahir, $alamat, $rt, $rw,
                    $kelurahan, $kecamatan, $kabupaten, $kategoriId, $unit, $jabatan,
                    $golongan, $jamKerjaId, $mulaiTugas, $jenjangId, $perguruan, $prodi, $tahunLulus,
                    $whatsapp, $nuptk, $sertifikasi
                ) {
                    // 1. Upsert User Account
                    $user = User::where('no_induk', $nipy)->first();
                    if (! $user) {
                        // Pastikan email unik jika akun baru
                        if (User::where('email', $email)->exists()) {
                            $cleanNipy = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nipy));
                            $email     = $cleanNipy . '_' . time() . '@primainsani.sch.id';
                        }

                        $user = User::create([
                            'name'      => $nama,
                            'no_induk'  => $nipy,
                            'email'     => $email,
                            'password'  => 'Password',
                            'is_active' => $isActive,
                        ]);
                    } else {
                        $userUpdate = [
                            'name'      => $nama,
                            'is_active' => $isActive,
                        ];
                        // Update email jika tidak bentrok dengan user lain
                        if ($email && ! User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
                            $userUpdate['email'] = $email;
                        }
                        $user->update($userUpdate);
                    }

                    // 2. Upsert Karyawan
                    $karyawan = Karyawan::find($nipy);
                    $isNew    = ! $karyawan;

                    if ($isNew) {
                        $karyawan = new Karyawan();
                        $karyawan->nipy = $nipy;
                    }

                    $karyawan->fill([
                        'nik'                                     => $nik,
                        'nama_lengkap'                            => $nama,
                        'gelar'                                   => $gelar,
                        'jk'                                      => $jk,
                        'tempat_lahir'                            => $tempatLahir,
                        'tanggal_lahir'                           => $tglLahir,
                        'alamat'                                  => $alamat,
                        'rt'                                      => $rt,
                        'rw'                                      => $rw,
                        'kelurahan'                               => $kelurahan,
                        'kecamatan'                               => $kecamatan,
                        'kabupaten'                               => $kabupaten,
                        'kepegawaian_master_kategori_id'          => $kategoriId,
                        'unit'                                    => $unit,
                        'jabatan'                                 => $jabatan,
                        'golongan'                                => $golongan,
                        'kepegawaian_master_jam_kerja_id'         => $jamKerjaId,
                        'mulai_tugas'                             => $mulaiTugas,
                        'akademik_pelengkap_jenjang_pendidikan_id' => $jenjangId,
                        'perguruan'                               => $perguruan,
                        'prodi'                                   => $prodi,
                        'tahun_lulus'                             => $tahunLulus,
                        'whatsapp'                                => $whatsapp,
                        'nuptk'                                   => $nuptk,
                        'sertifikasi'                             => $sertifikasi,
                    ]);

                    $karyawan->save();

                    $isNew ? $this->created++ : $this->updated++;
                });
            } catch (Exception $e) {
                $this->errors[] = "Baris {$line} ({$nipy} - {$nama}): " . $e->getMessage();
            }
        }

        // Hapus cache sidebar agar badge jumlah unit langsung update
        Cache::forget(SidebarComposer::CACHE_KEY);
    }

    /**
     * Resolusi ID master data dari input string/angka.
     */
    private function resolveLookup(Collection $collection, string $input, string $nameField, int $defaultId): int
    {
        if ($input === '') {
            return $defaultId;
        }

        // Jika user mengetikkan angka (ID)
        if (is_numeric($input)) {
            $found = $collection->firstWhere('id', (int) $input);
            if ($found) {
                return (int) $found->id;
            }
        }

        // Cari berdasarkan nama persis (case-insensitive)
        $found = $collection->first(fn ($item) => strcasecmp(trim((string) $item->$nameField), $input) === 0);
        if ($found) {
            return (int) $found->id;
        }

        // Cari berdasarkan kemiripan / substring
        $found = $collection->first(fn ($item) => stripos((string) $item->$nameField, $input) !== false);
        if ($found) {
            return (int) $found->id;
        }

        return $defaultId;
    }

    /**
     * Parse tanggal dari format Excel atau string teks.
     */
    private function parseDate(mixed $value, ?string $fallback = null): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return $fallback;
        }

        // Serial tanggal numerik dari Excel
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                // Abaikan dan coba parse sebagai string
            }
        }

        $str = trim((string) $value);

        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable) {
            return $fallback;
        }
    }
}
