<?php

namespace App\Models;

use App\Models\Admin\DataMaster\HariLibur as BaseHariLibur;

/**
 * Proxy model HariLibur untuk kompatibilitas namespace global App\Models\HariLibur.
 */
class HariLibur extends BaseHariLibur
{
    // Mewarisi seluruh relasi, casting, dan logika dari App\Models\Admin\DataMaster\HariLibur
}
