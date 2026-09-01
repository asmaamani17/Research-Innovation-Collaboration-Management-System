<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title') | Anugerah Penyelidikan</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#184290",
                        "background-light": "#f6f7f8",
                        "background-dark": "#121720",
                        "text-main": "#141A16",
                        "divider-subtle": "#B4BED4",
                        "sidebar-active": "#6C7CB3",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px",
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

        .active-nav {
            background-color: #6c7cb3;
            color: white !important;
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out, width 0.4s cubic-bezier(0.4, 0, 0.2, 1), overflow 0.4s cubic-bezier(0.4, 0, 0.2, 1), pointer-events 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
        }

        .main-content-transition {
            transition: margin-left 0.3s ease-in-out, width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content-full {
            margin-left: 0 !important;
        }

        @media (max-width: 768px) {
            .sidebar-mobile {
                position: fixed;
                z-index: 50;
                height: 100vh;
            }

            .main-content-mobile {
                margin-left: 0 !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-background-light font-display text-text-main">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden sidebar-transition"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-primary text-white flex flex-col shrink-0 fixed md:relative h-full z-50 sidebar-transition sidebar-mobile md:translate-x-0"
            style="width: 18rem; transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1), overflow 0.4s cubic-bezier(0.4, 0, 0.2, 1), pointer-events 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
            <div class="p-6 flex flex-col gap-8 h-full overflow-hidden">
                <!-- Profile Section with Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12 border-2 border-white/20"
                            data-alt="University logo with blue and white colors"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBk7jCJuymwFmvw7EoBG7cawFtxzkPCiw7HET2PqQaBUdeDZiCpkZgaEIv6Naw3AC_vTZ6bSnCOYWw-NSXZOLTDs2rdYzi3Tg2iiyEXlEs4VIvD0gyrKbS_ueIm3XUBxaWLoc_nt8cSJsWOe7y-VkC7EiNlz70AFwA_XqMZ5Z7LsyS-cTmXtVHRMhdQc-qljkiG_IWciPyjcumQnhiYXfTEcJLNFRZ9YXx1ow2rycZGJq7twDVUysiX9aKu_Pqw9YRFALO2aI90Ma0');">
                        </div>
                        <div class="flex flex-col min-w-0">
                            <p class="text-white/70 text-xs font-normal">Good to see you,</p>
                            <h1 class="text-white text-base font-bold leading-tight truncate">
                                {{ auth()->check() ? auth()->user()->name : 'Admin' }}
                            </h1>
                            <p class="text-white/50 text-xs">System Administrator</p>
                        </div>
                    </div>
                    <button id="sidebarToggle" class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-white">close</span>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-col gap-1 grow">
                    @php
                        $user = auth()->user();
                        $userRole = $user ? $user->role : '';
                        $isSuperadmin = $userRole === 'super_admin';
                        $isAdmin = $userRole === 'admin';
                        $isStaff = $userRole === 'section_staff';
                        
                        // Get current workspace from session or default to superadmin
                        $currentWorkspace = session('workspace', 'superadmin');
                        
                        // Get user's assigned modules
                        $userModules = $user ? $user->modules()->pluck('module_name')->toArray() : [];
                    @endphp

                    @if($isSuperadmin && $currentWorkspace === 'superadmin')
                        <!-- Super Admin Workspace Navigation -->
                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mb-2">Dashboard</p>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('superadmin.dashboard') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>

                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mt-6 mb-2">Applications</p>
                        <a href="{{ route('workspace.switch', 'awards') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ $currentWorkspace === 'awards' ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">emoji_events</span>
                            <p class="text-sm font-medium">Awards Management</p>
                        </a>
                        <a href="{{ route('workspace.switch', 'kpi') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ $currentWorkspace === 'kpi' ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">assessment</span>
                            <p class="text-sm font-medium">KPI Management</p>
                        </a>
                        <a href="{{ route('workspace.switch', 'projects') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ $currentWorkspace === 'projects' ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">science</span>
                            <p class="text-sm font-medium">Research Projects</p>
                        </a>
                        <a href="{{ route('workspace.switch', 'events') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ $currentWorkspace === 'events' ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">calendar_month</span>
                            <p class="text-sm font-medium">Events Management</p>
                        </a>
                        <a href="{{ route('workspace.switch', 'ip') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ $currentWorkspace === 'ip' ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">inventory_2</span>
                            <p class="text-sm font-medium">IP Management</p>
                        </a>

                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mt-6 mb-2">System</p>
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.users.*') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">manage_accounts</span>
                            <p class="text-sm font-medium">User Management</p>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">settings</span>
                            <p class="text-sm font-medium">System Settings</p>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">description</span>
                            <p class="text-sm font-medium">Audit Logs</p>
                        </a>

                    @elseif($isSuperadmin && $currentWorkspace === 'awards')
                        <!-- Awards Workspace Navigation -->
                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mb-2">Awards Management</p>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.staff.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">group</span>
                            <p class="text-sm font-medium">Staff Management</p>
                        </a>
                        <a href="{{ route('admin.faculty.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.faculty.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">school</span>
                            <p class="text-sm font-medium">Faculty List</p>
                        </a>
                        <a href="{{ route('admin.projects.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.projects.*') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">science</span>
                            <p class="text-sm font-medium">Projects</p>
                        </a>
                        <a href="{{ route('admin.events') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.events') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">calendar_month</span>
                            <p class="text-sm font-medium">Events</p>
                        </a>
                        <a href="{{ route('admin.awards') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.awards') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">emoji_events</span>
                            <p class="text-sm font-medium">Awards</p>
                        </a>
                        <a href="{{ route('admin.awards.import') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.awards.import') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">upload_file</span>
                            <p class="text-sm font-medium">Import Data</p>
                        </a>
                        <a href="{{ route('admin.awards.reports') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.awards.reports') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">bar_chart</span>
                            <p class="text-sm font-medium">Reports</p>
                        </a>

                        <div class="border-t border-white/20 my-4"></div>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">arrow_back</span>
                            <p class="text-sm font-medium">Back to Super Admin</p>
                        </a>

                    @elseif($isSuperadmin && $currentWorkspace === 'kpi')
                        <!-- KPI Workspace Navigation -->
                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mb-2">KPI Management</p>
                        <a href="{{ route('admin.kpi.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.kpi.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                        <a href="{{ route('admin.kpi.edit') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.kpi.edit') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">edit_note</span>
                            <p class="text-sm font-medium">Strategic Plans</p>
                        </a>
                        <a href="{{ route('admin.kpi.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">assessment</span>
                            <p class="text-sm font-medium">KPIs</p>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">insights</span>
                            <p class="text-sm font-medium">Indicators</p>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">trending_up</span>
                            <p class="text-sm font-medium">Achievements</p>
                        </a>
                        <a href="{{ route('admin.reports') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.reports') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">bar_chart</span>
                            <p class="text-sm font-medium">Reports</p>
                        </a>

                        <div class="border-t border-white/20 my-4"></div>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">arrow_back</span>
                            <p class="text-sm font-medium">Back to Super Admin</p>
                        </a>

                    @elseif($isSuperadmin && $currentWorkspace === 'projects')
                        <!-- Research Projects Workspace Navigation -->
                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mb-2">Research Projects</p>
                        <a href="{{ route('admin.projects.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.projects.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.staff.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">person</span>
                            <p class="text-sm font-medium">Researchers</p>
                        </a>
                        <a href="{{ route('admin.projects.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.projects.*') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">science</span>
                            <p class="text-sm font-medium">Projects</p>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">account_balance</span>
                            <p class="text-sm font-medium">Grants</p>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">menu_book</span>
                            <p class="text-sm font-medium">Publications</p>
                        </a>
                        <a href="{{ route('admin.reports') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.reports') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">bar_chart</span>
                            <p class="text-sm font-medium">Reports</p>
                        </a>

                        <div class="border-t border-white/20 my-4"></div>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">arrow_back</span>
                            <p class="text-sm font-medium">Back to Super Admin</p>
                        </a>

                    @elseif($isSuperadmin && $currentWorkspace === 'ip')
                        <!-- IP Workspace Navigation -->
                        <p class="text-white/50 text-xs font-semibold uppercase tracking-wider mb-2">IP Management</p>
                        <a href="{{ route('admin.ip.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.ip.dashboard') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                        <a href="{{ route('admin.ip.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.ip.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">inventory_2</span>
                            <p class="text-sm font-medium">Intellectual Properties</p>
                        </a>
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.staff.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">group</span>
                            <p class="text-sm font-medium">Staff Management</p>
                        </a>
                        <a href="{{ route('admin.faculty.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.faculty.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">school</span>
                            <p class="text-sm font-medium">Faculty List</p>
                        </a>
                        <a href="{{ route('admin.projects.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.projects.*') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">science</span>
                            <p class="text-sm font-medium">Projects</p>
                        </a>
                        <a href="{{ route('admin.ip.import') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.ip.import') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">upload_file</span>
                            <p class="text-sm font-medium">Import Data</p>
                        </a>
                        <a href="{{ route('admin.ip.reports') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.ip.reports') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">bar_chart</span>
                            <p class="text-sm font-medium">Reports</p>
                        </a>

                        <div class="border-t border-white/20 my-4"></div>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-white">arrow_back</span>
                            <p class="text-sm font-medium">Back to Super Admin</p>
                        </a>

                    @elseif($isAdmin)
                        @if(in_array('KPI', $userModules))
                            <a href="{{ route('admin.kpi.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.kpi.*') ? 'active-nav' : '' }}">
                                <span class="material-symbols-outlined text-white">assessment</span>
                                <p class="text-sm font-medium">KPI Dashboard</p>
                            </a>
                            <a href="{{ route('admin.kpi.edit') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.kpi.*') ? 'active-nav' : '' }}">
                                <span class="material-symbols-outlined text-white">assessment</span>
                                <p class="text-sm font-medium">Edit KPI</p>
                            </a>
                        @endif
                        @if(in_array('Awards', $userModules))
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                            <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.staff.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">group</span>
                            <p class="text-sm font-medium">Staff Management</p>
                        </a>
                        <a href="{{ route('admin.faculty.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.faculty.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">school</span>
                            <p class="text-sm font-medium">Faculty List</p>
                        </a>
                        <a href="{{ route('admin.projects.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.projects.*') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">science</span>
                            <p class="text-sm font-medium">Projects</p>
                        </a>
                        <a href="{{ route('admin.events') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.events') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">calendar_month</span>
                            <p class="text-sm font-medium">Events</p>
                        </a>
                        <a href="{{ route('admin.awards') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.awards') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">emoji_events</span>
                            <p class="text-sm font-medium">Awards</p>
                        </a>
                         <a href="{{ route('admin.import.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.import.*') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">upload_file</span>
                            <p class="text-sm font-medium">Import Data</p>
                        </a>
                        <a href="{{ route('admin.reports') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.reports') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">bar_chart</span>
                            <p class="text-sm font-medium">Reports</p>
                        </a>
                        @endif
                        @if(in_array('Products', $userModules) || in_array('Intellectual Property', $userModules))
                            <a href="{{ route('admin.projects.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.projects.*') ? 'active-nav' : '' }}">
                                <span class="material-symbols-outlined text-white">science</span>
                                <p class="text-sm font-medium">Projects</p>
                            </a>
                        @endif
                        @if(in_array('Community', $userModules) || in_array('STEM', $userModules))
                            <a href="{{ route('admin.events') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.events') ? 'active-nav' : '' }}">
                                <span class="material-symbols-outlined text-white">calendar_month</span>
                                <p class="text-sm font-medium">Events</p>
                            </a>
                        @endif
                    @elseif($isStaff)
                        <!-- Staff users - show basic navigation -->
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.staff.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">group</span>
                            <p class="text-sm font-medium">Staff Management</p>
                        </a>
                        <a href="{{ route('admin.faculty.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 cursor-pointer transition-colors {{ request()->routeIs('admin.faculty.index') ? 'active-nav' : '' }}">
                            <span class="material-symbols-outlined text-white">school</span>
                            <p class="text-sm font-medium">Faculty List</p>
                        </a>
                    @endif
                </nav>

                <!-- Footer Sidebar -->
                <div class="pt-4 border-t border-white/10">
                    <form action="{{ route('logout') }}" method="POST" class="contents">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-500/20 cursor-pointer transition-colors w-full text-left">
                            <span class="material-symbols-outlined text-white">logout</span>
                            <p class="text-sm font-medium">Logout</p>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main id="mainContent" class="flex-1 flex flex-col overflow-y-auto"
            style="width: calc(100% - 18rem); transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
            <!-- Top Bar with Toggle -->
            <header class="bg-white border-b border-divider-subtle/30 px-6 py-4 flex items-center gap-4">
                <button id="sidebarToggleDesktop"
                    class="hidden md:flex p-2 rounded-lg hover:bg-background-light transition-colors">
                    <span class="material-symbols-outlined text-text-main">menu</span>
                </button>
                <button id="sidebarToggleMobile"
                    class="md:hidden p-2 rounded-lg hover:bg-background-light transition-colors">
                    <span class="material-symbols-outlined text-text-main">menu</span>
                </button>
                <div class="flex-1"></div>

                <!-- Workspace Switcher in Header -->
                @if(auth()->check() && (is_string(auth()->user()->role) ? auth()->user()->role === 'super_admin' : auth()->user()->role->role_name === 'super_admin'))
                @php
                    $workspaceNames = [
                        'superadmin' => 'Super Admin',
                        'awards' => 'Awards Management',
                        'kpi' => 'KPI Management',
                        'projects' => 'Research Projects',
                        'events' => 'Events Management'
                    ];
                    $currentWorkspaceName = $workspaceNames[$currentWorkspace] ?? $workspaceNames['superadmin'];
                @endphp
                <!-- <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 bg-background-light rounded-lg hover:bg-background-light/80 transition-colors border border-divider-subtle/30">
                        <span class="text-sm font-medium text-text-main">{{ $currentWorkspaceName }}</span>
                        <span class="material-symbols-outlined text-divider-subtle text-sm transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 bg-white rounded-lg shadow-lg border border-divider-subtle/30 overflow-hidden z-50 w-64">
                        <p class="text-xs font-semibold text-divider-subtle uppercase tracking-wider px-4 py-2 bg-background-light">Select Workspace</p>
                        <a href="{{ route('workspace.switch', 'awards') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light transition-colors {{ $currentWorkspace === 'awards' ? 'bg-primary/10 text-primary' : 'text-text-main' }}">
                            <span class="material-symbols-outlined text-lg">emoji_events</span>
                            <span class="text-sm font-medium">Awards Management</span>
                        </a>
                        <a href="{{ route('workspace.switch', 'kpi') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light transition-colors {{ $currentWorkspace === 'kpi' ? 'bg-primary/10 text-primary' : 'text-text-main' }}">
                            <span class="material-symbols-outlined text-lg">assessment</span>
                            <span class="text-sm font-medium">KPI Management</span>
                        </a>
                        <a href="{{ route('workspace.switch', 'projects') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light transition-colors {{ $currentWorkspace === 'projects' ? 'bg-primary/10 text-primary' : 'text-text-main' }}">
                            <span class="material-symbols-outlined text-lg">science</span>
                            <span class="text-sm font-medium">Research Projects</span>
                        </a>
                        <a href="{{ route('workspace.switch', 'events') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light transition-colors {{ $currentWorkspace === 'events' ? 'bg-primary/10 text-primary' : 'text-text-main' }}">
                            <span class="material-symbols-outlined text-lg">calendar_month</span>
                            <span class="text-sm font-medium">Events Management</span>
                        </a>
                    </div>
                </div> -->
                @endif
            </header>

            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggleDesktop = document.getElementById('sidebarToggleDesktop');
            const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');
            const sidebarToggleClose = sidebar.querySelector('#sidebarToggle');

            let isSidebarOpen = true;

            function toggleSidebar() {
                isSidebarOpen = !isSidebarOpen;

                if (window.innerWidth >= 768) {
                    // Desktop behavior
                    if (isSidebarOpen) {
                        sidebar.style.width = '18rem'; // 72 in Tailwind = 18rem
                        sidebar.style.overflow = 'visible';
                        sidebar.style.pointerEvents = 'auto';
                        mainContent.style.width = 'calc(100% - 18rem)';
                        sidebarToggleDesktop.innerHTML = '<span class="material-symbols-outlined text-text-main">menu</span>';
                    } else {
                        sidebar.style.width = '0';
                        sidebar.style.overflow = 'hidden';
                        sidebar.style.pointerEvents = 'none';
                        mainContent.style.width = '100%';
                        sidebarToggleDesktop.innerHTML = '<span class="material-symbols-outlined text-text-main">menu_open</span>';
                    }
                } else {
                    // Mobile behavior remains the same
                    if (isSidebarOpen) {
                        sidebar.classList.remove('translate-x-[-100%]');
                        sidebarOverlay.classList.remove('hidden');
                    } else {
                        sidebar.classList.add('translate-x-[-100%]');
                        sidebarOverlay.classList.add('hidden');
                    }
                }
            }

            function closeSidebarMobile() {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('translate-x-[-100%]');
                    sidebarOverlay.classList.add('hidden');
                    isSidebarOpen = false;
                }
            }

            // Desktop toggle
            sidebarToggleDesktop?.addEventListener('click', toggleSidebar);

            // Mobile toggle
            sidebarToggleMobile?.addEventListener('click', toggleSidebar);

            // Mobile close button
            sidebarToggleClose?.addEventListener('click', closeSidebarMobile);

            // Overlay click to close
            sidebarOverlay?.addEventListener('click', closeSidebarMobile);

            // Handle window resize
            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebarOverlay.classList.add('hidden');
                    sidebar.style.position = 'relative';
                    mainContent.classList.remove('main-content-mobile');

                    if (!isSidebarOpen) {
                        sidebar.style.width = '0';
                        sidebar.style.overflow = 'hidden';
                        sidebar.style.pointerEvents = 'none';
                        mainContent.style.width = '100%';
                        sidebarToggleDesktop.innerHTML = '<span class="material-symbols-outlined text-text-main">menu_open</span>';
                    } else {
                        sidebar.style.width = '18rem';
                        sidebar.style.overflow = 'visible';
                        sidebar.style.pointerEvents = 'auto';
                        mainContent.style.width = 'calc(100% - 18rem)';
                        sidebarToggleDesktop.innerHTML = '<span class="material-symbols-outlined text-text-main">menu</span>';
                    }
                } else {
                    // Mobile
                    sidebar.style.width = '18rem';
                    sidebar.style.overflow = 'visible';
                    sidebar.style.pointerEvents = 'auto';
                    sidebar.style.position = 'fixed';
                    mainContent.style.width = '100%';
                    mainContent.classList.add('main-content-mobile');

                    if (!isSidebarOpen) {
                        sidebar.classList.add('translate-x-[-100%]');
                    } else {
                        sidebar.classList.remove('translate-x-[-100%]');
                    }
                }
            }

            window.addEventListener('resize', handleResize);

            // Initialize on load
            handleResize();

            // Close mobile sidebar when navigation links are clicked
            const navLinks = sidebar.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        closeSidebarMobile();
                    }
                });
            });

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.swal-delete-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: form.dataset.swalTitle || 'Are you sure?',
                        text: form.dataset.swalText || 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: form.dataset.swalConfirm || 'Yes, delete',
                        cancelButtonText: form.dataset.swalCancel || 'Cancel',
                        reverseButtons: true,
                        focusCancel: true,
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
</html>