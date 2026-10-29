<!-- Mobile Backdrop -->
<div id="mobileSidebarBackdrop"
    class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-xs transition-opacity duration-300 hidden opacity-0 lg:hidden"
    aria-hidden="true"></div>

<!-- Sidebar Container -->
<aside id="appSidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-900 border-r border-slate-800/90 text-slate-300 transition-all duration-300 ease-in-out -translate-x-full lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:w-68 shrink-0 shadow-2xl lg:shadow-none">

    <!-- Sidebar Header / Logo (Pinned Top) -->
    <div class="flex h-16 shrink-0 items-center justify-between px-5 border-b border-slate-800/80 bg-slate-950/40">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('storage/logo/samping.png') }}" class="h-9 w-auto object-contain brightness-110 logo-full"
                alt="Logo SIPIT Yayasan Prima Insani" onerror="this.src='{{ asset('logo.png') }}'; this.onerror=null;">
            <img src="{{ asset('logo.png') }}" class="h-9 w-9 object-contain brightness-110 logo-mini hidden"
                alt="Logo Mini" onerror="this.src='{{ asset('storage/logo/samping.png') }}'; this.onerror=null;">
        </a>
        <!-- Close button on mobile -->
        <button type="button" id="closeMobileSidebarBtn"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white lg:hidden"
            aria-label="Tutup Menu">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Menu (Scrolls ONLY if items overflow screen height) -->
    <div class="flex-1 overflow-y-auto min-h-0 px-4 py-4 space-y-6 sidebar-scroll">

        <!-- Section 1: Menu Utama -->
        <div>
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 menu-heading">
                Menu Utama
            </p>
            <nav class="space-y-1">
                <!-- Dashboard -->
                <div class="relative group/tooltip">
                    <a href="{{ route('home') }}"
                        class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('home') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('home') ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="menu-text">Dashboard</span>
                        </div>
                    </a>
                    <div class="sidebar-tooltip">Dashboard</div>
                </div>

                <!-- Data Master Dropdown -->
                @php
                    $isDataMasterActive = request()->is('data-master/kategori*', 'data-master/jam-kerja*', 'data-master/bulan*');
                @endphp
                <div class="has-submenu relative group/submenu">
                    <button type="button" data-sidebar-toggle="submenu-data-master"
                        aria-expanded="{{ $isDataMasterActive ? 'true' : 'false' }}"
                        class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isDataMasterActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ $isDataMasterActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7zm0 4h16M10 4v16" />
                            </svg>
                            <span class="menu-text">Data Master</span>
                        </div>
                        <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isDataMasterActive ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu list & flyout popup container -->
                    <div id="submenu-data-master"
                        class="{{ $isDataMasterActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1">
                        <div class="flyout-header hidden">
                            <span>Data Master</span>
                        </div>
                        <a href="{{ route('kategori.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('data-master/kategori*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Kategori Karyawan
                        </a>
                        <a href="{{ route('jam-kerja.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('data-master/jam-kerja*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Jam Kerja
                        </a>
                        <a href="{{ route('bulan.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('data-master/bulan*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Bulan
                        </a>
                    </div>
                </div>

                <!-- Karyawan Dropdown -->
                @php
                    $isKaryawanActive = request()->is('data-master/karyawan*');
                @endphp
                <div class="has-submenu relative group/submenu">
                    <button type="button" data-sidebar-toggle="submenu-karyawan"
                        aria-expanded="{{ $isKaryawanActive ? 'true' : 'false' }}"
                        class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isKaryawanActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ $isKaryawanActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="menu-text">Karyawan</span>
                        </div>
                        <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isKaryawanActive ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu list & flyout popup container -->
                    <div id="submenu-karyawan"
                        class="{{ $isKaryawanActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1">
                        <div class="flyout-header hidden">
                            <span>Karyawan</span>
                        </div>
                        <a href="{{ route('karyawan.index') }}"
                            class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('karyawan.index') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <span>Semua Karyawan</span>
                        </a>
                        @foreach ($sidebarKategori ?? [] as $kat)
                            @php
                                $isUnitAktif = (isset($unitAktif) && $unitAktif === $kat['nama']) ||
                                    request()->is('data-master/karyawan/unit/' . urlencode($kat['nama'])) ||
                                    request()->is('data-master/karyawan/unit/' . $kat['nama']);
                            @endphp
                            <a href="{{ route('karyawan.unit', $kat['nama']) }}"
                                class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $isUnitAktif ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                <span class="truncate">{{ $kat['nama'] }}</span>
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">
                                    {{ $kat['jumlah'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Presensi Dropdown -->
                @php
                    $isPresensiActive = request()->is('presensi*', 'kegiatan*');
                @endphp
                <div class="has-submenu relative group/submenu">
                    <button type="button" data-sidebar-toggle="submenu-presensi"
                        aria-expanded="{{ $isPresensiActive ? 'true' : 'false' }}"
                        class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isPresensiActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ $isPresensiActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="menu-text">Presensi</span>
                        </div>
                        <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isPresensiActive ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu list & flyout popup container -->
                    <div id="submenu-presensi"
                        class="{{ $isPresensiActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1 max-h-[80vh] overflow-y-auto">
                        <div class="flyout-header hidden">
                            <span>Presensi</span>
                        </div>
                        <a href="{{ route('presensi.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('presensi.index') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Riwayat Presensi
                        </a>
                        <a href="{{ route('presensi.rekap') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('presensi.rekap') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Rekap Bulanan
                        </a>
                        <a href="{{ route('presensi.matriks') }}"
                            class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('presensi.matriks') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <span>Matriks Presensi</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-orange-500 text-white">GRID</span>
                        </a>
                        <a href="{{ route('presensi.statistik-tahunan') }}"
                            class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('presensi.statistik-tahunan') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <span>Statistik Tahunan</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-orange-500 text-white">NEW</span>
                        </a>
                        <a href="{{ route('kegiatan.scanner') }}"
                            class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('kegiatan.scanner') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <span>Scan QR Kegiatan</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500 text-white">SCAN</span>
                        </a>
                        <a href="{{ route('kegiatan.index') }}"
                            class="flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('kegiatan.index', 'kegiatan.show', 'kegiatan.create', 'kegiatan.edit') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <span>Agenda Kegiatan</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-500 text-slate-900">ADMIN</span>
                        </a>
                    </div>
                </div>

                <!-- Administrasi & Fasilitas Dropdown -->
                @php
                    $isAdministrasiActive = request()->is('fasilitas*', 'surat-keputusan*', 'penggajian*');
                @endphp
                <div class="has-submenu relative group/submenu">
                    <button type="button" data-sidebar-toggle="submenu-administrasi"
                        aria-expanded="{{ $isAdministrasiActive ? 'true' : 'false' }}"
                        class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isAdministrasiActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ $isAdministrasiActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="menu-text">Administrasi & Fasilitas</span>
                        </div>
                        <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isAdministrasiActive ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu list & flyout popup container -->
                    <div id="submenu-administrasi"
                        class="{{ $isAdministrasiActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1">
                        <div class="flyout-header hidden">
                            <span>Administrasi & Fasilitas</span>
                        </div>
                        <a href="{{ route('fasilitas.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('fasilitas*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Fasilitas
                        </a>
                        <a href="{{ route('surat-keputusan.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('surat-keputusan*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Surat Keputusan (SK)
                        </a>
                        <a href="{{ route('penggajian.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('penggajian*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Penggajian
                        </a>
                    </div>
                </div>

                <!-- Kinerja & Pengembangan Dropdown -->
                @php
                    $isKinerjaActive = request()->is('rapot-kinerja*', 'tupoksi*', 'diklat*', 'sertifikat-penghargaan*');
                @endphp
                <div class="has-submenu relative group/submenu">
                    <button type="button" data-sidebar-toggle="submenu-kinerja"
                        aria-expanded="{{ $isKinerjaActive ? 'true' : 'false' }}"
                        class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isKinerjaActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 {{ $isKinerjaActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            <span class="menu-text">Kinerja & Pengembangan</span>
                        </div>
                        <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isKinerjaActive ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu list & flyout popup container -->
                    <div id="submenu-kinerja"
                        class="{{ $isKinerjaActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1">
                        <div class="flyout-header hidden">
                            <span>Kinerja & Pengembangan</span>
                        </div>
                        <a href="{{ route('rapot-kinerja.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('rapot-kinerja*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Rapot Kinerja
                        </a>
                        <a href="{{ route('tupoksi.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('tupoksi*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Tupoksi
                        </a>
                        <a href="{{ route('diklat.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('diklat*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Diklat
                        </a>
                        <a href="{{ route('sertifikat-penghargaan.index') }}"
                            class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->is('sertifikat-penghargaan*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            Penghargaan
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Section 2: Akun & Logout (Pinned Bottom) -->
    <div class="shrink-0 p-3 border-t border-slate-800/80 bg-slate-950/40 relative group/tooltip">
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
            class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-all">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4 shrink-0 text-rose-400 group-hover:text-rose-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="menu-text">Keluar Sistem</span>
            </div>
        </a>
        <div class="sidebar-tooltip">Keluar Sistem</div>
        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>
