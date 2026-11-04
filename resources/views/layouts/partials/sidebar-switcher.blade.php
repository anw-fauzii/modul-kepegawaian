@php
    $sidebarUser = Auth::user();
    $isSuperAdmin = $sidebarUser && $sidebarUser->hasRole('super admin');
    $isInUserArea = request()->is('user/*', 'dashboard/user*') || request('view') === 'user';
@endphp

@if ($isSuperAdmin)
    <!-- Super Admin Mode Switcher Button (Segmented Pill) -->
    <div class="mb-4">
        <div class="p-1 rounded-xl bg-slate-950/80 border border-slate-800/90 flex items-center gap-1 shadow-inner">
            <!-- Tombol Mode Admin -->
            <a href="{{ route('home') }}"
                class="flex-1 flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ !$isInUserArea ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                title="Beralih ke Panel Manajemen Admin">
                <svg class="h-3.5 w-3.5 shrink-0 {{ !$isInUserArea ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="menu-text">Admin</span>
            </a>

            <!-- Tombol Mode Pegawai -->
            <a href="{{ route('dashboard.user') }}"
                class="flex-1 flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $isInUserArea ? 'bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md shadow-orange-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                title="Beralih ke Area Pegawai Pribadi">
                <svg class="h-3.5 w-3.5 shrink-0 {{ $isInUserArea ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="menu-text">Pegawai</span>
            </a>
        </div>
    </div>
@endif

