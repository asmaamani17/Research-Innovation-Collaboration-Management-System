<style>
  .products-form {
    --paper:#F6F7F8; --paper-raised:#FFFFFF; --ink:#141A16; --ink-soft:#667085;
    --line:#DDE2EA; --gold:#184290; --radius:12px;
    min-height:100%; padding:0; background:transparent; color:var(--ink);
    font-family:Inter,sans-serif;
  }
  .products-form *, .products-form *::before, .products-form *::after { box-sizing:border-box; }
  .products-form h1 { margin:0 0 20px; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em; }
  .products-form .page-title {
    margin:0; font-size:30px; font-weight:900; line-height:1.25; letter-spacing:-.025em;
  }
  .products-form .page-description {
    margin:6px 0 0; color:#B4BED4; font-size:14px; font-weight:500; line-height:1.5;
  }
  .products-form .card {
    padding:24px; border:1px solid rgb(180 190 212 / 30%); border-radius:var(--radius);
    background:var(--paper-raised); box-shadow:0 1px 3px rgb(16 24 40 / 5%);
  }
  .products-form .btn {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    min-height:44px; padding:10px 18px; border:1px solid #184290; border-radius:8px;
    background:#184290; color:#fff; font-size:14px; font-weight:700;
    text-decoration:none; cursor:pointer;
  }
  .products-form .btn.secondary { border-color:rgb(180 190 212 / 50%); background:#fff; color:#344054; }
  .products-form .btn.secondary:hover { background:#F2F4F7; }
  .products-form .form-field { margin-bottom:16px; min-width:0; }
  .products-form .form-field label {
    display:block; margin-bottom:8px; color:#344054; font-size:14px;
    font-weight:500; letter-spacing:0; text-transform:none;
  }
  .products-form .form-field input,
  .products-form .form-field select,
  .products-form .form-field textarea {
    width:100%; min-height:46px; padding:11px 14px; border:1px solid rgb(180 190 212 / 45%); border-radius:8px;
    background:#fff; color:var(--ink); font-family:inherit; font-size:14px;
    outline:none; transition:border-color .15s ease,box-shadow .15s ease;
  }
  .products-form .form-field input:focus,
  .products-form .form-field select:focus,
  .products-form .form-field textarea:focus {
    border-color:#184290; box-shadow:0 0 0 3px rgb(24 66 144 / 12%);
  }
  .products-form .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .products-form .form-grid .full { grid-column:1/-1; }
  .products-form .error-list { margin:0 32px 16px; padding:14px 16px; border:1px solid #FECDCA; border-radius:8px; background:#FEF3F2; color:#B42318; }
  .products-form > header { padding:32px 32px 24px; }
  .products-form > .form-card { margin:0 32px 32px; overflow:hidden; }
  .products-form .form-card form { padding:28px; }
  .products-form .section-title {
    font-size:18px; font-weight:700; color:#344054; margin:0 0 8px 0; padding-bottom:8px;
    border-bottom:1px solid rgb(180 190 212 / 30%);
  }
  .products-form .section-description {
    font-size:14px; color:#667085; margin:0 0 20px 0;
  }
  @media(max-width:640px) {
    .products-form .form-grid { grid-template-columns:1fr; gap:0; }
    .products-form .form-grid .full { grid-column:auto; }
    .products-form .card { padding:15px; }
    .products-form > .form-card { margin-right:16px !important;margin-left:16px !important; }
    .products-form .error-list { margin-right:16px;margin-left:16px; }
    .products-form > header { padding:24px 16px 20px; }
    .products-form .form-card form { padding:18px; }
  }
</style>

<div class="products-form">
@if($errors->any())
  <div class="error-list">
    <strong>Please review the following errors:</strong>
    <ul style="margin:6px 0 0;padding-left:18px;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<header>
  <div class="flex items-center justify-between gap-4 mb-2">
    <div>
      <h1 class="page-title">{{ $method === 'POST' ? 'Add Product' : 'Edit Product' }}</h1>
      <p class="page-description">{{ $method === 'POST' ? 'Create a new product record' : 'Update product record' }}</p>
    </div>
    <a href="{{ route('admin.products.records') }}" class="flex min-w-[160px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-gray-200 text-gray-700 text-sm font-bold shadow-lg hover:bg-gray-300 transition-all">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      <span>Back to Products</span>
    </a>
  </div>
</header>

<div class="card form-card">
  <form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="form-field">
            <label for="trl_level">TRL Level <span style="color:#B42318">*</span></label>
            <select id="trl_level" name="trl_level" required>
                <option value="">Select TRL</option>
                @foreach(range(1, 9) as $level)
                    <option value="{{ $level }}" @selected((string) old('trl_level', $product->trl_level) === (string) $level)>TRL {{ $level }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-field">
            <label for="product_year">Year</label>
            <input id="product_year" type="number" name="product_year" min="1900" max="2100" value="{{ old('product_year', $product->product_year) }}" placeholder="Enter year">
        </div>

        <div class="form-field full">
            <label for="product">Product <span style="color:#B42318">*</span></label>
            <textarea id="product" name="product" rows="2" required placeholder="Enter product name">{{ old('product', $product->product) }}</textarea>
        </div>

        <div class="form-field full">
            <label for="researcher">Researcher(s)</label>
            <textarea id="researcher" name="researcher" rows="2" placeholder="Enter researcher names">{{ old('researcher', $product->researcher) }}</textarea>
        </div>

        <div class="form-field">
            <label for="faculty">Faculty</label>
            <input id="faculty" name="faculty" maxlength="100" value="{{ old('faculty', $product->faculty) }}" placeholder="Enter faculty">
        </div>

        <div class="form-field">
            <label for="category">Category</label>
            <textarea id="category" name="category" rows="2" placeholder="Enter category">{{ old('category', $product->category) }}</textarea>
        </div>

        <div class="form-field">
            <label for="status">Status</label>
            <input id="status" name="status" maxlength="100" value="{{ old('status', $product->status) }}" placeholder="e.g. In-Development, Stagnant, Commercial">
        </div>

        <div class="form-field">
            <label for="action">Action</label>
            <input id="action" name="action" maxlength="150" value="{{ old('action', $product->action) }}" placeholder="e.g. Prototype Stage">
        </div>

        <div class="form-field full">
            <label for="abstract">Abstract / Description</label>
            <textarea id="abstract" name="abstract" rows="5" placeholder="Enter product description">{{ old('abstract', $product->abstract) }}</textarea>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 mt-6 pt-6 border-t border-divider-subtle/20">
        <a href="{{ route('admin.products.records') }}" class="btn secondary">Cancel</a>
        <button type="submit" class="btn">
            <span class="material-symbols-outlined text-lg">save</span> {{ $method === 'POST' ? 'Save Product' : 'Save Changes' }}
        </button>
    </div>
  </form>
</div>
</div>
