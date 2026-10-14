<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Community, STEM &amp; Industry') | RICM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc =
      "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
  </script>
  <style>
    :root{
      --paper:#F3F1EA; --paper-raised:#FFFFFF; --ink:#1F2A24; --ink-soft:#6B7268;
      --line:#E3DFD3; --gold:#B08A3E; --stem:#3E7A5C; --community:#B0623E; --industri:#6E5AA8;
      --danger:#B0453E; --radius:14px;
    }
    *{box-sizing:border-box;}
    body{margin:0;font-family:'Inter',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;}
    h1{font-size:30px;font-weight:700;margin:0 0 20px;}
    .card{background:var(--paper-raised);border:1px solid var(--line);border-radius:var(--radius);padding:24px;}
    .btn{
      display:inline-block;background:var(--ink);color:#fff;border:none;border-radius:8px;
      padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none;cursor:pointer;
    }
    .btn.secondary{background:#fff;color:var(--ink);border:1px solid var(--line);}
    .btn.danger{background:var(--danger);}
    table{width:100%;border-collapse:collapse;font-size:13.5px;}
    thead th{
      text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);
      padding:10px 12px;border-bottom:2px solid var(--ink);
    }
    tbody td{padding:12px;border-bottom:1px solid var(--line);vertical-align:top;line-height:1.5;}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;}
    .badge.community{background:#F3E3D8;color:var(--community);}
    .badge.stem{background:#DCEDE3;color:var(--stem);}
    .badge.industri{background:#E6E1F3;color:var(--industri);}
    .row-actions a{color:var(--ink-soft);font-size:12px;text-decoration:underline;margin-right:8px;}
    .row-actions a.edit-link:hover{color:var(--gold);}
    .row-actions a.delete-link:hover{color:var(--danger);}
    .form-field{margin-bottom:16px;}
    .form-field label{display:block;font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);font-weight:600;margin-bottom:5px;}
    .form-field input, .form-field select, .form-field textarea{
      width:100%;font-family:'Inter',sans-serif;font-size:13.5px;padding:9px 10px;border-radius:8px;
      border:1px solid var(--line);background:#fff;color:var(--ink);
    }
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .form-grid .full{grid-column:1/-1;}
    .alert-success{background:#DCEDE3;color:var(--stem);padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13.5px;}
    .error-list{background:#F3E3D8;color:var(--danger);padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13px;}
    .top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    .nav-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;border-bottom:1px solid var(--line);padding-bottom:12px;}
    .nav-bar .nav-links{display:flex;gap:18px;}
    .nav-bar a{color:var(--ink-soft);text-decoration:none;font-size:13.5px;font-weight:600;}
    .nav-bar a.active{color:var(--ink);border-bottom:2px solid var(--gold);padding-bottom:12px;margin-bottom:-13px;}
    .nav-bar .panduan-link{color:var(--gold);}
    .stem-app{display:flex;height:100vh;min-height:100vh;overflow:hidden;}
    .stem-sidebar{position:relative;z-index:50;display:flex;flex:0 0 288px;flex-direction:column;width:288px;height:100vh;padding:24px;background:#243b34;color:#fff;overflow:hidden;transition:flex-basis .3s ease,width .3s ease,padding .3s ease,transform .3s ease;}
    .stem-sidebar.is-collapsed{flex-basis:0;width:0;padding-right:0;padding-left:0;}
    .stem-brand{display:flex;align-items:center;gap:12px;padding:0 0 22px;border-bottom:1px solid rgb(255 255 255 / 14%);}
    .stem-brand-mark{display:grid;width:42px;height:42px;flex:0 0 42px;place-items:center;border-radius:12px;background:var(--gold);color:#243b34;font-size:23px;font-weight:700;}
    .stem-brand-name{margin:0;font-size:17px;font-weight:700;line-height:1.1;}
    .stem-brand-caption{margin:5px 0 0;color:rgb(255 255 255 / 65%);font-size:10px;letter-spacing:.09em;text-transform:uppercase;}
    .stem-profile{display:flex;align-items:center;gap:12px;padding:20px 0 0;}
    .stem-profile-avatar{display:grid;width:42px;height:42px;flex:0 0 42px;place-items:center;border:2px solid rgb(255 255 255 / 24%);border-radius:50%;background:rgb(255 255 255 / 10%);color:#fff;font-size:16px;font-weight:700;}
    .stem-profile-greeting{margin:0 0 3px;color:rgb(255 255 255 / 65%);font-size:11px;}
    .stem-profile-name{max-width:175px;margin:0;overflow:hidden;color:#fff;font-size:13px;font-weight:600;text-overflow:ellipsis;white-space:nowrap;}
    .stem-profile-role{margin:3px 0 0;color:rgb(255 255 255 / 55%);font-size:10px;}
    .stem-nav{display:flex;flex:1;flex-direction:column;gap:5px;padding-top:24px;}
    .stem-nav-label{margin:0 10px 8px;color:rgb(255 255 255 / 48%);font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;}
    .stem-nav a,.stem-sidebar-footer a,.stem-sidebar-footer form button{display:flex;min-height:44px;align-items:center;gap:12px;padding:10px 12px;border-radius:8px;color:rgb(255 255 255 / 78%);font-size:13px;font-weight:500;text-decoration:none;transition:color .15s ease,background .15s ease;}
    .stem-nav a:hover,.stem-sidebar-footer a:hover{color:#fff;background:rgb(255 255 255 / 10%);}
    .stem-nav a.active{color:#fff;background:rgb(176 138 62 / 24%);box-shadow:inset 3px 0 var(--gold);}
    .stem-nav-icon{width:20px;color:var(--gold);font-size:16px;text-align:center;}
    .stem-sidebar-footer{display:flex;flex-direction:column;gap:4px;padding-top:14px;border-top:1px solid rgb(255 255 255 / 14%);}
    .stem-sidebar-footer form{margin:0;}
    .stem-sidebar-footer button{width:100%;border:0;background:transparent;text-align:left;cursor:pointer;font-family:inherit;}
    .stem-sidebar-footer button:hover{color:#fff;background:rgb(176 69 62 / 24%);}
    .stem-workspace{display:flex;min-width:0;min-height:0;flex:1;flex-direction:column;overflow-y:auto;transition:width .3s ease;}
    .stem-header{position:sticky;top:0;z-index:10;display:flex;min-height:64px;flex:0 0 64px;align-items:center;gap:14px;padding:0 24px;border-bottom:1px solid var(--line);background:#fffefa;}
    .stem-header-title{margin:0;color:var(--ink);font-size:16px;font-weight:600;}
    .stem-content{width:100%;max-width:1600px;margin:0 auto;padding:30px clamp(18px,3vw,44px) 42px;}
    .stem-content>h1,.top-bar h1{font-weight:700;}
    .card{box-shadow:0 5px 18px rgb(36 49 44 / 4%);}
    #communitySidebarOverlay{display:none;}
    .stem-menu-toggle{display:inline-flex;width:38px;height:38px;flex:0 0 38px;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:8px;color:var(--ink);background:#fff;cursor:pointer;}
    @media(max-width:900px){.stem-content{padding-top:24px}.stem-content [style*="grid-template-columns:repeat(4"],.stem-content [style*="grid-template-columns:1.4fr"]{grid-template-columns:repeat(2,minmax(0,1fr))!important}}
    @media(max-width:768px){.stem-app{overflow:visible}.stem-sidebar{position:fixed;top:0;bottom:0;left:0;width:min(288px,86vw);height:100dvh;transform:translateX(-102%);transition:transform .25s ease}.stem-sidebar.is-open{transform:translateX(0)}.stem-sidebar.is-collapsed{flex-basis:auto;width:min(288px,86vw);padding:24px;transform:translateX(-102%)}.stem-sidebar.is-collapsed.is-open{transform:translateX(0)}#communitySidebarOverlay.is-visible{position:fixed;z-index:40;inset:0;display:block;background:rgb(15 24 20 / 48%)}.stem-workspace{min-height:100vh;overflow:visible}.stem-header{min-height:58px;flex-basis:58px;padding:0 16px}.stem-header-title{font-size:16px}.stem-content{padding:20px 14px 30px}.top-bar{align-items:flex-start;flex-direction:column}.stem-content [style*="grid-template-columns"]{grid-template-columns:minmax(0,1fr)!important}.form-grid{grid-template-columns:1fr;gap:0}.card{padding:15px}}
  </style>
</head>
<body>
  <div class="stem-app">
    <div id="communitySidebarOverlay" aria-hidden="true"></div>
    <aside class="stem-sidebar" id="communitySidebar" aria-label="Community, STEM and Industry navigation">
      <div class="stem-brand">
        <div class="stem-brand-mark" aria-hidden="true">R</div>
        <div>
          <p class="stem-brand-name">RICE Research</p>
          <p class="stem-brand-caption">Community · STEM · Industry</p>
        </div>
      </div>
      <div class="stem-profile">
        <div class="stem-profile-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
        <div>
          <p class="stem-profile-greeting">Good to see you,</p>
          <p class="stem-profile-name">{{ auth()->user()->name ?? 'Admin' }}</p>
          <p class="stem-profile-role">System Administrator</p>
        </div>
      </div>
      <nav class="stem-nav">
        <p class="stem-nav-label">Workspace</p>
        <a href="{{ route('admin.stem.dashboard') }}" class="{{ request()->routeIs('admin.stem.dashboard') ? 'active' : '' }}">
          <span class="stem-nav-icon" aria-hidden="true">◫</span>
          <span>Dashboard</span>
        </a>
      </nav>
      <div class="stem-sidebar-footer">
        <a href="{{ route('superadmin.dashboard') }}">
          <span class="stem-nav-icon" aria-hidden="true">←</span>
          <span>Back to Super Admin</span>
        </a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit">
            <span class="stem-nav-icon" aria-hidden="true">↪</span>
            <span>Logout</span>
          </button>
        </form>
      </div>
    </aside>
    <div class="stem-workspace" id="communityMainContent">
      <header class="stem-header">
        <button type="button" class="stem-menu-toggle" id="communitySidebarToggle" aria-label="Toggle navigation" aria-controls="communitySidebar" aria-expanded="true">☰</button>
        <p class="stem-header-title">Community, STEM &amp; Industry Management</p>
      </header>
      <main class="stem-content">
        @if(session('success'))
          <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
          <div class="error-list">
            <strong>Please review the following errors:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        @yield('content')
      </main>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const sidebar = document.getElementById('communitySidebar');
      const overlay = document.getElementById('communitySidebarOverlay');
      const toggle = document.getElementById('communitySidebarToggle');
      const isMobile = function () {
        return window.matchMedia('(max-width: 768px)').matches;
      };
      function setSidebarState() {
        const mobileOpen = sidebar.classList.contains('is-open');
        const collapsed = sidebar.classList.contains('is-collapsed');
        overlay.classList.toggle('is-visible', isMobile() && mobileOpen);
        toggle.setAttribute('aria-expanded', String(isMobile() ? mobileOpen : !collapsed));
      }
      toggle.addEventListener('click', function () {
        if (isMobile()) {
          sidebar.classList.toggle('is-open');
        } else {
          sidebar.classList.toggle('is-collapsed');
        }
        setSidebarState();
      });
      overlay.addEventListener('click', function () {
        sidebar.classList.remove('is-open');
        setSidebarState();
      });
      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isMobile()) {
          sidebar.classList.remove('is-open');
          setSidebarState();
        }
      });
      window.addEventListener('resize', function () {
        sidebar.classList.remove('is-open');
        setSidebarState();
      });
      setSidebarState();
    });
  </script>
  @stack('scripts')

</body>
</html>