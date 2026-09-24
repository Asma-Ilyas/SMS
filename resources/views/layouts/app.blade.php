<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Management System')</title>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /*
         * Print rules: hide the sidebar, navbar, and anything marked .no-print,
         * and let <main> take the full printed page.
         * Any page that wants ONLY part of its own content to print (not all
         * of <main>) should wrap that part in <div id="print-area">...</div>
         * — if #print-area exists on the page, only it is shown; otherwise
         * the whole of <main> prints.
         */
        @media print {
            #app-sidebar,
            #app-navbar,
            .no-print {
                display: none !important;
            }

            body, .flex, .flex-1 {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background: #fff !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            /* If a page defines #print-area, hide everything else inside <main>
               and show only that area. */
            body:has(#print-area) main > *:not(#print-area) {
                display: none !important;
            }
            body:has(#print-area) #print-area {
                display: block !important;
            }
        }
    </style>
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <div id="app-sidebar">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 bg-gray-50">
            <!-- Navbar -->
            <div id="app-navbar">
                @include('layouts.navbar')
            </div>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>