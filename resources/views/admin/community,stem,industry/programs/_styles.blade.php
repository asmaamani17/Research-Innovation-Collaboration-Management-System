<style>
  .community-programs {
    --paper:#F6F7F8; --paper-raised:#FFFFFF; --ink:#141A16; --ink-soft:#667085;
    --line:#DDE2EA; --gold:#184290; --stem:#287A55; --community:#A34D2D;
    --industri:#6845A5; --danger:#B42318; --radius:12px;
    min-height:100%; padding:0; background:transparent; color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .community-programs *, .community-programs *::before, .community-programs *::after { box-sizing:border-box; }
  .community-programs h1 { margin:0 0 20px; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em; }
  .community-programs .page-title {
    margin:0; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em;
  }
  .community-programs .page-description {
    margin:6px 0 0; color:#B4BED4; font-size:14px; font-weight:500; line-height:1.5;
  }
  .community-programs .content-description { color:#B4BED4 !important; }
  .community-programs .card {
    padding:24px; border:1px solid rgb(180 190 212 / 30%); border-radius:var(--radius);
    background:var(--paper-raised); box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .community-programs .btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:44px; padding:10px 18px; border:1px solid #184290; border-radius:8px;
    background:#184290; color:#fff; font-size:14px; font-weight:700;
    text-decoration:none; cursor:pointer;
  }
  .community-programs .btn.secondary { border-color:rgb(180 190 212 / 50%); background:#fff; color:#344054; }
  .community-programs .btn.secondary:hover { background:#F2F4F7; }
  .community-programs .btn.danger { border-color:var(--danger); background:var(--danger); color:#fff; }
  .community-programs .top-bar {
    display:flex; justify-content:space-between; align-items:center; gap:16px;
    margin:0; padding:32px 32px 24px;
  }
  .community-programs table { width:100%; border-collapse:collapse; font-size:14px; }
  .community-programs thead th {
    padding:16px 24px; border-bottom:1px solid rgb(180 190 212 / 30%); background:#F6F7F8;
    color:#344054; font-size:12px; font-weight:700; letter-spacing:.04em;
    text-align:left; text-transform:uppercase; white-space:nowrap;
  }
  .community-programs tbody tr { border-bottom:1px solid rgb(180 190 212 / 20%); transition:background .15s ease; }
  .community-programs tbody tr:hover { background:#F8FAFC; }
  .community-programs tbody td { padding:16px 24px; line-height:1.5; vertical-align:top; color:#344054; }
  .community-programs .form-field { margin-bottom:16px; min-width:0; }
  .community-programs .form-field label {
    display:block; margin-bottom:8px; color:#344054; font-size:14px;
    font-weight:500; letter-spacing:0; text-transform:none;
  }
  .community-programs .form-field input,
  .community-programs .form-field select,
  .community-programs .form-field textarea {
    width:100%; min-height:46px; padding:11px 14px; border:1px solid rgb(180 190 212 / 45%); border-radius:8px;
    background:#fff; color:var(--ink); font-family:inherit; font-size:14px;
    outline:none; transition:border-color .15s ease,box-shadow .15s ease;
  }
  .community-programs .form-field input:focus,
  .community-programs .form-field select:focus,
  .community-programs .form-field textarea:focus {
    border-color:#184290; box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .community-programs .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .community-programs .form-grid .full { grid-column:1/-1; }
  .community-programs .alert-success { margin:0 32px 16px; padding:14px 16px; border:1px solid #ABEFC6; border-radius:8px; background:#ECFDF3; color:#067647; }
  .community-programs .error-list { margin:0 32px 16px; padding:14px 16px; border:1px solid #FECDCA; border-radius:8px; background:#FEF3F2; color:var(--danger); }
  .community-programs .filter-card { margin:0 32px 24px !important; }
  .community-programs .records-card { margin:0 32px 32px; padding:0; overflow:hidden; }
  .community-programs .records-toolbar { display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:18px 24px; }
  .community-programs .records-table-wrap { overflow-x:auto; }
  .community-programs .records-toolbar select {
    min-height:38px;padding:6px 30px 6px 10px;border:1px solid #D0D5DD;border-radius:7px;
    background:#fff;color:#344054;font:inherit;
  }
  .community-programs .sort-link { color:#344054;font-weight:700; }
  .community-programs .sort-icon { color:#667085; }
  .community-programs .dropdown-panel {
    border-color:#E4E7EC;border-radius:10px;box-shadow:0 8px 20px rgb(16 24 40 / 12%);
  }
  .community-programs .dropdown-panel a:hover,
  .community-programs .dropdown-panel button:hover { background:#F2F4F7; }
  .community-programs .action-trigger,.community-programs .evidence-badge {
    border-color:#D0D5DD;background:#fff;color:#475467;
  }
  .community-programs .action-trigger:hover,.community-programs .evidence-badge:hover {
    background:#F2F4F7;
  }
  .community-programs .id-badge { border-color:#D0D5DD;background:#F9FAFB;color:#344054; }
  .community-programs .chip { border-color:#D0D5DD;background:#F9FAFB;color:#475467; }
  .community-programs .badge { display:inline-flex;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:700; }
  .community-programs .badge.community { background:#EFF8FF;color:#175CD3; }
  .community-programs .badge.stem { background:#ECFDF3;color:#027A48; }
  .community-programs .badge.industri { background:#F4F3FF;color:#5925DC; }
  .community-programs .pager { padding:16px 24px;border-top:1px solid rgb(180 190 212 / 30%); }
  .community-programs .pager-btn { border-color:#D0D5DD;background:#fff;color:#344054; }
  .community-programs a.pager-btn:hover,.community-programs .pager-btn.pager-active { border-color:#184290;background:#184290;color:#fff; }
  .community-programs .pager-btn.pager-disabled { background:#F9FAFB; }
  .community-programs > header { padding:32px 32px 24px; }
  .community-programs > .form-card { margin:0 32px 32px; overflow:hidden; }
  .community-programs .form-card form { padding:28px; }
  .community-programs .ocr-card { margin:0 32px 24px; }
  @media(max-width:900px) {
    .community-programs [style*="grid-template-columns:repeat(4"],
    .community-programs [style*="grid-template-columns:repeat(4,"] {
      grid-template-columns:repeat(2,minmax(0,1fr)) !important;
    }
  }
  @media(max-width:640px) {
    .community-programs .top-bar { align-items:flex-start; flex-direction:column; padding:24px 16px 20px; }
    .community-programs .form-grid { grid-template-columns:1fr; gap:0; }
    .community-programs .form-grid .full { grid-column:auto; }
    .community-programs .card { padding:15px; }
    .community-programs .filter-card,.community-programs .records-card,
    .community-programs > .form-card,.community-programs .ocr-card {
      margin-right:16px !important;margin-left:16px !important;
    }
    .community-programs .filter-card { margin-bottom:18px !important; }
    .community-programs .records-card { padding:0; }
    .community-programs [style*="grid-template-columns"] { grid-template-columns:minmax(0,1fr) !important; }
    .community-programs .records-toolbar { align-items:flex-start;flex-direction:column;padding:16px; }
    .community-programs thead th,.community-programs tbody td { padding:12px 16px; }
    .community-programs .alert-success,.community-programs .error-list { margin-right:16px;margin-left:16px; }
    .community-programs > header { padding:24px 16px 20px; }
    .community-programs .form-card form { padding:18px; }
  }
</style>
