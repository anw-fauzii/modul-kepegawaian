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
                alt="Logo PION Yayasan Prima Insani" onerror="this.src='{{ asset('logo.png') }}'; this.onerror=null;">
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
    <div class="flex-1 overflow-y-auto min-h-0 px-4 py-4 space-y-4 sidebar-scroll">

        @php
            $sidebarUser = Auth::user();
            $isSuperAdmin = $sidebarUser && $sidebarUser->hasRole('super admin');
            
            // Konteks aktif: Apakah sedang membuka halaman Pegawai Pribadi?
            $isInUserArea = request()->is('user/*', 'dashboard/user*') || request('view') === 'user';
            
            // Tampilkan Menu Pegawai jika:
            // 1. User biasa (bukan super admin) -> selalu lihat Menu Pegawai
            // 2. Super Admin yang sedang berada di area personalnya (Mode Pegawai)
            $showUserSidebar = !$isSuperAdmin || $isInUserArea;
        @endphp
        
        @include('layouts.partials.sidebar-switcher')

        @if ($showUserSidebar)
            @include('layouts.partials.sidebar-user')
        @else
            @include('layouts.partials.sidebar-admin')
        @endif

    </div>

    <!-- Section: Akun & Logout (Pinned Bottom) -->
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
