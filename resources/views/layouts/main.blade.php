<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title') | Anugerah Penyelidikan</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#184290",
                        "background-light": "#f6f7f8",
                        "background-dark": "#121720",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-background-light min-h-screen flex flex-col font-display">
    <!-- Top Navigation Bar -->
    <header class="w-full bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="text-primary size-8">
                        <!-- <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_330)">
                                <path clip-rule="evenodd" d="M24 0.757355L47.2426 24L24 47.2426L0.757355 24L24 0.757355ZM21 35.7574V12.2426L9.24264 24L21 35.7574Z" fill="currentColor" fill-rule="evenodd"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_6_330">
                                    <rect fill="white" height="48" width="48"></rect>
                                </clipPath>
                            </defs>
                        </svg> -->
                    </div>
                    <!-- <span class="text-gray-900 text-lg font-bold tracking-tight">Anugerah Penyelidikan</span> -->
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a class="text-gray-600 text-sm font-medium hover:text-primary transition-colors" href="#">University Portal</a>
                    <a class="text-gray-600 text-sm font-medium hover:text-primary transition-colors" href="#">Research Hub</a>
                    <a class="text-gray-600 text-sm font-medium hover:text-primary transition-colors" href="#">Support</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col items-center justify-center px-4 py-12">
        @yield('content')
    </main>

    <!-- Page Footer -->
    <footer class="w-full py-6 text-center text-gray-400 text-xs">
        <p>© 2024 Universiti Teknikal Malaysia Melaka. All rights reserved.</p>
        <p class="mt-1">Research Management Information System (RAMS) v2.4.1</p>
    </footer>
    
    @stack('scripts')
</body>
</html>