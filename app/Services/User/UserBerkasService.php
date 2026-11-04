<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserBerkasService
{
    protected const DISK = 'private';

    /**
     * Menampilkan preview berkas fisik milik user login di browser secara inline.
     *
     * @param User $user
     * @param Model $record
     * @param string $fileField
     * @return BinaryFileResponse|StreamedResponse
     */
    public function preview(User $user, Model $record, string $fileField = 'berkas'): BinaryFileResponse|StreamedResponse
    {
        $this->authorizeOwnership($user, $record);

        $path = $record->{$fileField};
        abort_if(empty($path), 404, 'Berkas dokumen tidak tersedia.');

        $disk = Storage::disk(self::DISK)->exists($path) ? self::DISK : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404, 'Berkas fisik tidak ditemukan di storage server.');

        return Storage::disk($disk)->response($path);
    }

    /**
     * Mengunduh berkas fisik milik user login.
     *
     * @param User $user
     * @param Model $record
     * @param string $fileField
     * @return BinaryFileResponse|StreamedResponse
     */
    public function download(User $user, Model $record, string $fileField = 'berkas'): BinaryFileResponse|StreamedResponse
    {
        $this->authorizeOwnership($user, $record);

        $path = $record->{$fileField};
        abort_if(empty($path), 404, 'Berkas dokumen tidak tersedia.');

        $disk = Storage::disk(self::DISK)->exists($path) ? self::DISK : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404, 'Berkas fisik tidak ditemukan di storage server.');

        return Storage::disk($disk)->download($path, basename($path));
    }

    /**
     * Memvalidasi kepemilikan dokumen berdasarkan NIPY pegawai login.
     *
     * @param User $user
     * @param Model $record
     * @return void
     */
    public function authorizeOwnership(User $user, Model $record): void
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        abort_if(
            !$nipy || (string) $record->karyawan_nipy !== (string) $nipy,
            403,
            'Akses Ditolak: Anda hanya dapat mengakses dokumen pribadi Anda.'
        );
    }
}

