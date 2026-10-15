<style>
  .user-form {
    --ink:#141A16;
    --line:#DDE2EA;
    min-height:100%;
    color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .user-form *, .user-form *::before, .user-form *::after { box-sizing:border-box; }
  .user-form > header { padding:32px 32px 24px; }
  .user-form .page-title {
    margin:0;
    font-size:30px;
    font-weight:900;
    line-height:1.25;
    letter-spacing:-.025em;
  }
  .user-form .page-description {
    margin:6px 0 0;
    color:#B4BED4;
    font-size:14px;
    font-weight:500;
    line-height:1.5;
  }
  .user-form .back-link {
    display:inline-flex;
    min-height:44px;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:10px 18px;
    border:1px solid rgb(180 190 212 / 50%);
    border-radius:8px;
    background:#fff;
    color:#344054;
    font-size:14px;
    font-weight:700;
    text-decoration:none;
  }
  .user-form .back-link:hover, .user-form .secondary:hover { background:#F2F4F7; }
  .user-form .form-card {
    margin:0 32px 32px;
    overflow:hidden;
    padding:0;
    border:1px solid rgb(180 190 212 / 30%);
    border-radius:12px;
    background:#fff;
    box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .user-form .form-card form { padding:28px; }
  .user-form .form-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
  }
  .user-form .form-field { min-width:0; margin-bottom:16px; }
  .user-form .form-field label {
    display:block;
    margin-bottom:8px;
    color:#344054;
    font-size:14px;
    font-weight:500;
    letter-spacing:0;
    text-transform:none;
  }
  .user-form .form-field input,
  .user-form .form-field select {
    width:100%;
    min-height:46px;
    padding:11px 14px;
    border:1px solid rgb(180 190 212 / 45%);
    border-radius:8px;
    background:#fff;
    color:var(--ink);
    font-family:inherit;
    font-size:14px;
    outline:none;
    transition:border-color .15s ease,box-shadow .15s ease;
  }
  .user-form .form-field input:focus,
  .user-form .form-field select:focus {
    border-color:#184290;
    box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .user-form .error-list {
    margin:0 0 20px;
    padding:14px 16px;
    border:1px solid #FECDCA;
    border-radius:8px;
    background:#FEF3F2;
    color:#B42318;
    font-size:14px;
  }
  .user-form .module-access {
    margin-top:8px;
    padding-top:20px;
    border-top:1px solid rgb(180 190 212 / 30%);
  }
  .user-form .module-grid {
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:12px;
    padding:16px;
    border:1px solid rgb(180 190 212 / 30%);
    border-radius:10px;
    background:#F6F7F8;
  }
  .user-form .module-option {
    display:flex;
    min-width:0;
    align-items:center;
    gap:12px;
    padding:12px;
    border:1px solid rgb(180 190 212 / 30%);
    border-radius:8px;
    background:#fff;
    cursor:pointer;
    transition:border-color .15s ease,box-shadow .15s ease;
  }
  .user-form .module-option:hover { border-color:#184290; }
  .user-form .module-checkbox { flex:0 0 auto; accent-color:#184290; }
  .user-form .form-actions {
    display:flex;
    justify-content:flex-end;
    gap:12px;
    margin-top:24px;
    padding-top:24px;
    border-top:1px solid rgb(180 190 212 / 30%);
  }
  .user-form .button {
    display:inline-flex;
    min-height:44px;
    align-items:center;
    justify-content:center;
    padding:10px 18px;
    border:1px solid #184290;
    border-radius:8px;
    background:#184290;
    color:#fff;
    font-size:14px;
    font-weight:700;
    text-decoration:none;
    cursor:pointer;
  }
  .user-form .button:hover { background:#123574; }
  .user-form .button.secondary {
    border-color:rgb(180 190 212 / 50%);
    background:#fff;
    color:#344054;
  }
  @media(max-width:800px) {
    .user-form .module-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
  }
  @media(max-width:640px) {
    .user-form > header { padding:24px 16px 20px; }
    .user-form .form-card { margin:0 16px 24px; }
    .user-form .form-card form { padding:18px; }
    .user-form .form-grid, .user-form .module-grid { grid-template-columns:1fr; }
    .user-form .form-actions { flex-direction:column-reverse; }
    .user-form .form-actions > * { width:100%; }
  }
</style>
