<?php

namespace App\Services\Admin\Penggajian;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Penggajian\Penggajian;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PenggajianService
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Penggajian::query()
            ->with([
                'karyawan:nipy,nama_lengkap,gelar,foto',
                'bulan:id,nama_bulan,akademik_master_tahun_ajaran_id',
                'bulan.tahunAjaran:id,nama_tahun_ajaran,semester',
            ])
            ->filter($filters)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getById(Penggajian $penggajian): Penggajian
    {
        return $penggajian->load([
            'karyawan.kategori',
            'karyawan.user',
            'bulan.tahunAjaran',
        ]);
    }

    public function getStatistik(array $filters = []): object
    {
        $row = Penggajian::query()
            ->filter($filters)
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(penerimaan),0) as penerimaan, COALESCE(SUM(potongan),0) as potongan, COALESCE(SUM(gaji_bersih),0) as gaji_bersih')
            ->first();

        return (object) [
            'total_slip'      => (int) $row->total,
            'total_penerimaan' => (float) $row->penerimaan,
            'total_potongan'  => (float) $row->potongan,
            'total_gaji_bersih' => (float) $row->gaji_bersih,
        ];
    }

    public function create(array $data): Penggajian
    {
        return DB::transaction(function () use ($data) {
            return Penggajian::create($this->withSnapshot($data));
        });
    }

    public function update(Penggajian $penggajian, array $data): Penggajian
    {
        $this->ensureEditable($penggajian);

        DB::transaction(function () use ($penggajian, $data) {
            $penggajian->update($this->withSnapshot($data));
        });

        return $penggajian->fresh();
    }

    public function updateStatus(Penggajian $penggajian, string $status): Penggajian
    {
        $penggajian->update(['status' => $status]);

        return $penggajian;
    }

    public function delete(Penggajian $penggajian): bool
    {
        $this->ensureEditable($penggajian);

        return (bool) $penggajian->delete();
    }

    /**
     * Slip yang sudah dibayar dikunci agar riwayat keuangan tidak berubah.
     */
    protected function ensureEditable(Penggajian $penggajian): void
    {
        if ($penggajian->status === 'dibayar') {
            throw new Exception('Slip gaji berstatus "Dibayar" tidak dapat diubah atau dihapus. Ubah status terlebih dahulu jika perlu koreksi.');
        }
    }

    /**
     * Unit, jabatan & golongan diisi dari data karyawan bila tidak diisi manual.
     */
    protected function withSnapshot(array $data): array
    {
        $karyawan = Karyawan::query()
            ->select('nipy', 'unit', 'jabatan', 'golongan')
            ->find($data['karyawan_nipy']);

        foreach (['unit', 'jabatan', 'golongan'] as $field) {
            if (empty($data[$field]) && $karyawan) {
                $data[$field] = $karyawan->{$field};
            }
        }

        return $data;
    }

    public function getJenisList(): array
    {
        return Penggajian::JENIS;
    }

    public function getJenisShortList(): array
    {
        return Penggajian::JENIS_SHORT;
    }

    public function getStatusList(): array
    {
        return Penggajian::STATUS;
    }

    public function getPenerimaanList(): array
    {
        return Penggajian::PENERIMAAN;
    }

    public function getPotonganList(): array
    {
        return Penggajian::POTONGAN;
    }
}
