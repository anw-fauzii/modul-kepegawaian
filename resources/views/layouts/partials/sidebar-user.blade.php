<div>
    <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 menu-heading">
        Menu Pegawai
    </p>
    <nav class="space-y-1">
        <!-- Dashboard Pegawai -->
        <div class="relative group/tooltip">
            <a href="{{ route('dashboard.user') }}"
                class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.user') || request('view') === 'user' ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                <div class="flex items-center gap-3">
                    <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('dashboard.user') || request('view') === 'user' ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="menu-text">Dashboard</span>
                </div>
            </a>
            <div class="sidebar-tooltip">Dashboard</div>
        </div>

        <!-- Presensi Saya -->
        <div class="relative group/tooltip">
            <a href="{{ route('user.presensi.index') }}"
                class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('user.presensi*') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                <div class="flex items-center gap-3">
                    <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('user.presensi*') ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="menu-text">Presensi Saya</span>
                </div>
            </a>
            <div class="sidebar-tooltip">Presensi Saya</div>
        </div>

        <!-- Pengajuan Cuti -->
        <div class="relative group/tooltip">
            <a href="{{ route('user.cuti.index') }}"
                class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('user.cuti*') ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                <div class="flex items-center gap-3">
                    <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('user.cuti*') ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="menu-text">Pengajuan Cuti</span>
                </div>
            </a>
            <div class="sidebar-tooltip">Pengajuan Cuti</div>
        </div>

        <!-- Administrasi SDM Dropdown (Bertingkat) -->
        @php
            $isUserAdministrasiActive = request()->routeIs('user.fasilitas*', 'user.surat-keputusan*', 'user.penggajian*') ||
                request()->is('user/fasilitas*', 'user/surat-keputusan*', 'user/penggajian*');
        @endphp
        <div class="has-submenu relative group/submenu">
            <button type="button" data-sidebar-toggle="submenu-user-administrasi"
                aria-expanded="{{ $isUserAdministrasiActive ? 'true' : 'false' }}"
                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isUserAdministrasiActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                <div class="flex items-center gap-3">
                    <svg class="h-4 w-4 shrink-0 {{ $isUserAdministrasiActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="menu-text">Administrasi SDM</span>
                </div>
                <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isUserAdministrasiActive ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="submenu-user-administrasi"
                class="{{ $isUserAdministrasiActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1">
                <div class="flyout-header hidden">
                    <span>Administrasi SDM</span>
                </div>
                <a href="{{ route('user.fasilitas.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.fasilitas*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Fasilitas Saya
                </a>
                <a href="{{ route('user.surat-keputusan.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.surat-keputusan*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Surat Keputusan (SK / ST)
                </a>
                <a href="{{ route('user.penggajian.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.penggajian*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Slip Gaji Saya
                </a>
            </div>
        </div>

        <!-- Perjalanan Karir Dropdown (Bertingkat) -->
        @php
            $isUserKinerjaActive = request()->routeIs('user.rapot-kinerja*', 'user.tupoksi*', 'user.diklat*', 'user.penghargaan*') ||
                request()->is('user/rapot-kinerja*', 'user/tupoksi*', 'user/diklat*', 'user/penghargaan*');
        @endphp
        <div class="has-submenu relative group/submenu">
            <button type="button" data-sidebar-toggle="submenu-user-kinerja"
                aria-expanded="{{ $isUserKinerjaActive ? 'true' : 'false' }}"
                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $isUserKinerjaActive ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/80' }}">
                <div class="flex items-center gap-3">
                    <svg class="h-4 w-4 shrink-0 {{ $isUserKinerjaActive ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span class="menu-text">Perjalanan Karir</span>
                </div>
                <svg class="menu-chevron h-3.5 w-3.5 text-slate-400 transition-transform duration-200 {{ $isUserKinerjaActive ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="submenu-user-kinerja"
                class="{{ $isUserKinerjaActive ? '' : 'hidden' }} submenu-flyout mt-1 space-y-1 pl-4 border-l border-slate-800 ml-5 py-1">
                <div class="flyout-header hidden">
                    <span>Perjalanan Karir</span>
                </div>
                <a href="{{ route('user.rapot-kinerja.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.rapot-kinerja*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Rapot Kinerja
                </a>
                <a href="{{ route('user.tupoksi.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.tupoksi*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Tupoksi Saya
                </a>
                <a href="{{ route('user.diklat.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.diklat*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Diklat & Pelatihan
                </a>
                <a href="{{ route('user.penghargaan.index') }}"
                    class="block px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('user.penghargaan*') ? 'text-orange-400 bg-orange-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    Sertifikat & Penghargaan
                </a>
            </div>
        </div>
    </nav>
</div>

