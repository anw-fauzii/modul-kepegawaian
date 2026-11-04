<?php

namespace App\Services\Admin\Cuti;

use App\Models\Admin\Cuti\Cuti;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CutiService
{
    /**
     * Ambil data cuti dengan filter, relasi, dan pagination.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Cuti::query()
            ->with(['karyawan', 'jenisCuti', 'approver'])
            ->filter($filters)
            ->latest('awal_cuti')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Ambil semua data cuti.
     */
    public function getAll(): Collection
    {
        return Cuti::query()
            ->with(['karyawan', 'jenisCuti', 'approver'])
            ->latest('awal_cuti')
            ->get();
    }

    /**
     * Ambil satu record cuti berdasarkan ID.
     */
    public function getById(int $id): Cuti
    {
        return Cuti::query()
            ->with(['karyawan', 'jenisCuti', 'approver'])
            ->findOrFail($id);
    }

    /**
     * Simpan pengajuan cuti baru.
     */
    public function store(array $data): Cuti
    {
        return DB::transaction(function () use ($data) {
            // Hitung otomatis durasi jumlah hari jika belum ada
            if (empty($data['jumlah_hari']) && ! empty($data['awal_cuti']) && ! empty($data['akhir_cuti'])) {
                $jenisCuti = ! empty($data['kepegawaian_master_jenis_cuti_id'])
                    ? \App\Models\Admin\DataMaster\JenisCuti::find($data['kepegawaian_master_jenis_cuti_id'])
                    : null;
                $isMelahirkan = $jenisCuti?->isMelahirkan() ?? false;
                $data['jumlah_hari'] = Cuti::hitungJumlahHari($data['awal_cuti'], $data['akhir_cuti'], null, $isMelahirkan);
            }

            // Jika status ACC bernilai true saat dibuat, catat waktu dan user
            if (! empty($data['acc']) && $data['acc'] == true) {
                $data['tgl_acc'] = now();
                $data['acc_by'] = auth()->id();
            }

            return Cuti::create($data);
        });
    }

    /**
     * Perbarui data cuti yang ada.
     */
    public function update(Cuti $cuti, array $data): Cuti
    {
        return DB::transaction(function () use ($cuti, $data) {
            // Hitung ulang jumlah hari jika tanggal berubah
            if (! empty($data['awal_cuti']) && ! empty($data['akhir_cuti'])) {
                $jenisCutiId = $data['kepegawaian_master_jenis_cuti_id'] ?? $cuti->kepegawaian_master_jenis_cuti_id;
                $jenisCuti = $jenisCutiId ? \App\Models\Admin\DataMaster\JenisCuti::find($jenisCutiId) : null;
                $isMelahirkan = $jenisCuti?->isMelahirkan() ?? false;
                $data['jumlah_hari'] = Cuti::hitungJumlahHari($data['awal_cuti'], $data['akhir_cuti'], null, $isMelahirkan);
            }

            // Kelola status acc
            if (isset($data['acc'])) {
                $isAcc = filter_var($data['acc'], FILTER_VALIDATE_BOOLEAN);
                if ($isAcc && ! $cuti->acc) {
                    $data['tgl_acc'] = now();
                    $data['acc_by'] = auth()->id();
                } elseif (! $isAcc && $cuti->acc) {
                    $data['tgl_acc'] = null;
                    $data['acc_by'] = null;
                }
            }

            $cuti->update($data);

            return $cuti->fresh(['karyawan', 'jenisCuti', 'approver']);
        });
    }

    /**
     * Hapus data cuti.
     */
    public function delete(Cuti $cuti): bool
    {
        return DB::transaction(fn () => $cuti->delete());
    }

    /**
     * Toggle status persetujuan (ACC <-> Menunggu ACC).
     */
    public function toggleAcc(Cuti $cuti, ?string $catatan = null, ?int $accByUserId = null): Cuti
    {
        return DB::transaction(function () use ($cuti, $catatan, $accByUserId) {
            $newStatus = ! $cuti->acc;

            $updateData = [
                'acc' => $newStatus,
            ];

            if ($catatan !== null) {
                $updateData['catatan_acc'] = $catatan;
            }

            if ($newStatus) {
                $updateData['tgl_acc'] = now();
                $updateData['acc_by'] = $accByUserId ?? auth()->id();
            } else {
                $updateData['tgl_acc'] = null;
                $updateData['acc_by'] = null;
            }

            $cuti->update($updateData);

            return $cuti->fresh(['karyawan', 'jenisCuti', 'approver']);
        });
    }

    /**
     * Dapatkan ringkasan statistik cuti untuk KPI card.
     */
    public function getRekapStatistik(): array
    {
        $currentYear = Carbon::now()->year;

        $totalCuti = Cuti::whereYear('awal_cuti', $currentYear)->count();
        $disetujui = Cuti::whereYear('awal_cuti', $currentYear)->where('acc', true)->count();
        $menunggu  = Cuti::whereYear('awal_cuti', $currentYear)->where('acc', false)->count();
        $totalHari = (int) Cuti::whereYear('awal_cuti', $currentYear)->where('acc', true)->sum('jumlah_hari');

        return [
            'total'      => $totalCuti,
            'disetujui'  => $disetujui,
            'menunggu'   => $menunggu,
            'total_hari' => $totalHari,
            'tahun'      => $currentYear,
        ];
    }
}
