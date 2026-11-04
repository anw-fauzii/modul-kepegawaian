<!doctype html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dynamic Title -->
    @yield('title', '<title>PION Kepegawaian - Yayasan Prima Insani</title>')

    <!-- Meta SEO & Open Graph -->
    <meta name="description" content="Sistem Informasi Kepegawaian Yayasan Prima Insani — Presensi, Pegawai, Kinerja, dan Kegiatan.">
    <meta property="og:title" content="PION Kepegawaian Yayasan Prima Insani">
    <meta property="og:description" content="Kelola absensi, data pegawai, kegiatan, dan kinerja yayasan secara modern dan terpadu.">
    <meta property="og:type" content="website">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome & Third Party Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('Icon-font-7-stroke/assets/Pe-icon-7-stroke.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- DataTables Tailwind CSS Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">

    <!-- Tailwind CSS & App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Global 3-Dots Action Dropdown Styles */
        .action-dropdown { position: relative; display: inline-flex; align-items: center; justify-content: center; }
        .btn-action-dots { width: 34px !important; height: 34px !important; min-width: 34px !important; border-radius: 9px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; background-color: #f8fafc !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; font-size: 0.9rem !important; cursor: pointer !important; transition: all 0.18s ease !important; padding: 0 !important; margin: 0 !important; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important; }
        .btn-action-dots:hover, .action-dropdown.open .btn-action-dots { background-color: #fff7ed !important; color: #ea580c !important; border-color: #fdba74 !important; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.14) !important; }
        .action-dropdown-menu { display: none; position: fixed; min-width: 175px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.14), 0 4px 10px -2px rgba(15, 23, 42, 0.07); padding: 6px; z-index: 9999; text-align: left; }
        .action-dropdown.open .action-dropdown-menu { display: block; }
        .action-dropdown-menu form { display: block !important; width: 100% !important; margin: 0 !important; }
        .action-dropdown-item { width: 100%; display: flex !important; align-items: center !important; gap: 10px !important; padding: 7px 10px !important; border-radius: 8px !important; font-size: 0.8125rem !important; font-weight: 600 !important; color: #334155 !important; background: transparent !important; border: 0 !important; text-decoration: none !important; cursor: pointer !important; transition: all 0.15s ease !important; text-align: left !important; white-space: nowrap !important; }
        .action-dropdown-item:hover { background-color: #f8fafc !important; color: #0f172a !important; }
        .action-dropdown-item .action-item-icon { width: 26px; height: 26px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.76rem; flex-shrink: 0; }
        .action-dropdown-item.item-view .action-item-icon { background-color: #e0f2fe; color: #0284c7; }
        .action-dropdown-item.item-view:hover { background-color: #f0f9ff !important; color: #0369a1 !important; }
        .action-dropdown-item.item-edit .action-item-icon { background-color: #eef2ff; color: #4f46e5; }
        .action-dropdown-item.item-edit:hover { background-color: #eef2ff !important; color: #4338ca !important; }
        .action-dropdown-item.item-success .action-item-icon { background-color: #dcfce7; color: #15803d; }
        .action-dropdown-item.item-success:hover { background-color: #f0fdf4 !important; color: #15803d !important; }
        .action-dropdown-item.item-warning .action-item-icon { background-color: #fef3c7; color: #b45309; }
        .action-dropdown-item.item-warning:hover { background-color: #fffbeb !important; color: #b45309 !important; }
        .action-dropdown-item.item-delete { color: #e11d48 !important; }
        .action-dropdown-item.item-delete .action-item-icon { background-color: #ffe4e6; color: #e11d48; }
        .action-dropdown-item.item-delete:hover { background-color: #fff1f2 !important; color: #be123c !important; }
        .action-dropdown-item.item-locked { color: #94a3b8 !important; cursor: not-allowed !important; }
        .action-dropdown-item.item-locked .action-item-icon { background-color: #f1f5f9; color: #94a3b8; }
        .action-dropdown-divider { height: 1px; background-color: #f1f5f9; margin: 4px 0; }

        /* Global Modal Overlay Adapter */
        .modal { display: none; position: fixed; inset: 0; z-index: 1055; overflow-x: hidden; overflow-y: auto; outline: 0; background-color: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); padding: 1rem; }
        .modal.show, .modal.is-open { display: flex !important; align-items: center; justify-content: center; }
        .modal-dialog { position: relative; width: 100%; max-width: 520px; margin: auto; pointer-events: auto; }
        .modal-backdrop { display: none !important; }
    </style>

    @stack('css')
</head>

<body class="h-full bg-slate-50 text-slate-800 antialiased font-sans selection:bg-orange-500 selection:text-white"
    data-success="{{ session('success') }}" data-error="{{ session('error') }}"
    data-warning="{{ session('warning') }}" data-status="{{ session('status') }}"
    data-message="{{ session('message') }}">

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loader-overlay d-none">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <p class="text-xs font-semibold text-slate-700">Proses sedang berlangsung, mohon tunggu...</p>
        </div>
    </div>

    <!-- App Layout Container -->
    <div class="min-h-screen flex bg-slate-50">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div id="appMainWrapper" class="flex-1 flex flex-col min-w-0 min-h-screen transition-all duration-300">
            <!-- Top Navbar -->
            @include('layouts.navbar')

            <!-- Main Page View Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('layouts.footer')
        </div>
    </div>

    @stack('crud')

    <!-- Essential Core JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Global Application Scripts (DataTables, Toastr, SweetAlert) -->
    <script>
        $(document).ready(function() {
            // Toastr Setup
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3500",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // DataTables Global Configuration
            const dtDefaultConfig = {
                processing: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari data...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    infoEmpty: "Data tidak ditemukan",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang sesuai",
                    paginate: {
                        first: "«",
                        previous: "‹",
                        next: "›",
                        last: "»"
                    }
                }
            };

            $('.datatable-default, #myTable, #myTable2, #myTable4').DataTable($.extend({}, dtDefaultConfig, {
                ordering: false,
                lengthMenu: [[25, 50, 100, -1], ['25', '50', '100', 'Semua']]
            }));

            $('.datatable-ordered, #myTable3, #myTable5').DataTable($.extend({}, dtDefaultConfig, {
                ordering: true,
                lengthMenu: [[25, 50, 100, -1], ['25', '50', '100', 'Semua']]
            }));

            $('.datatable-custom, #myTable6').DataTable($.extend({}, dtDefaultConfig, {
                ordering: false,
                searching: false,
                lengthMenu: [[25, 50, 100, -1], ['25', '50', '100', 'Semua']]
            }));

            // Flash Notification Messages
            const $body = $('body');
            const successMsg = $body.data('success');
            const errorMsg = $body.data('error');
            const warningMsg = $body.data('warning');
            const statusMsg = $body.data('status');
            const messageMsg = $body.data('message');

            if (successMsg) toastr.success(successMsg);
            if (errorMsg) toastr.error(errorMsg);
            if (warningMsg) toastr.warning(warningMsg);
            if (statusMsg) toastr.info(statusMsg);
            if (messageMsg && !successMsg && !errorMsg) toastr.info(messageMsg);
        });

        // Global 3-Dots Action Dropdown Handler (Position Fixed to prevent table overflow clipping)
        function closeAllActionDropdowns() {
            document.querySelectorAll('.action-dropdown.open').forEach(function(el) {
                el.classList.remove('open');
            });
        }

        document.addEventListener('click', function(e) {
            const dotsBtn = e.target.closest('.btn-action-dots');
            if (dotsBtn) {
                e.preventDefault();
                e.stopPropagation();
                const wrapper = dotsBtn.closest('.action-dropdown');
                if (!wrapper) return;

                const isOpen = wrapper.classList.contains('open');
                closeAllActionDropdowns();

                if (!isOpen) {
                    wrapper.classList.add('open');
                    const menu = wrapper.querySelector('.action-dropdown-menu');
                    if (menu) {
                        const rect = dotsBtn.getBoundingClientRect();
                        const menuWidth = menu.offsetWidth || 180;
                        const menuHeight = menu.offsetHeight || 160;
                        const viewportHeight = window.innerHeight;
                        const viewportWidth = window.innerWidth;

                        let left = rect.right - menuWidth;
                        if (left < 10) left = 10;
                        if (left + menuWidth > viewportWidth - 10) {
                            left = viewportWidth - menuWidth - 10;
                        }

                        let top = rect.bottom + 6;
                        if (top + menuHeight > viewportHeight - 12 && rect.top > menuHeight + 12) {
                            top = rect.top - menuHeight - 6;
                        }

                        menu.style.left = left + 'px';
                        menu.style.top = top + 'px';
                    }
                }
                return;
            }

            if (!e.target.closest('.action-dropdown-menu')) {
                closeAllActionDropdowns();
            }
        });

        window.addEventListener('scroll', closeAllActionDropdowns, true);
        window.addEventListener('resize', closeAllActionDropdowns);

        // Global SweetAlert Delete Handler
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-button');
            if (deleteBtn) {
                e.preventDefault();
                closeAllActionDropdowns();
                Swal.fire({
                    title: 'Konfirmasi Hapus Data',
                    text: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Data',
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger mx-1 px-4 py-2 text-sm rounded-lg',
                        cancelButton: 'btn btn-secondary mx-1 px-4 py-2 text-sm rounded-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteBtn.closest('form').submit();
                    }
                });
            }
        });
    </script>

    @yield('js')
</body>

</html>
