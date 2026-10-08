<div class="space-y-8 p-5 sm:p-8">
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="mb-1 font-bold">Please fix these fields:</p>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section>
        <h2 class="text-lg font-bold text-text-main">Product Information</h2>
        <p class="mt-1 text-sm text-divider-subtle">Record the product and its technology readiness level.</p>
        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="trl_level" class="mb-2 block text-sm font-medium text-text-main">TRL Level <span class="text-red-500">*</span></label>
                <select id="trl_level" name="trl_level" required class="w-full rounded-lg border border-divider-subtle/40 bg-white px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="">Select TRL</option>
                    @foreach(range(1, 9) as $level)
                        <option value="{{ $level }}" @selected((string) old('trl_level', $product->trl_level) === (string) $level)>TRL {{ $level }}</option>
                    @endforeach
                </select>
                @error('trl_level')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="product_year" class="mb-2 block text-sm font-medium text-text-main">Year</label>
                <input id="product_year" type="number" name="product_year" min="1900" max="2100" value="{{ old('product_year', $product->product_year) }}" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('product_year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="product" class="mb-2 block text-sm font-medium text-text-main">Product <span class="text-red-500">*</span></label>
                <textarea id="product" name="product" rows="2" required class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old('product', $product->product) }}</textarea>
                @error('product')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="researcher" class="mb-2 block text-sm font-medium text-text-main">Researcher(s)</label>
                <textarea id="researcher" name="researcher" rows="2" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old('researcher', $product->researcher) }}</textarea>
                @error('researcher')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="faculty" class="mb-2 block text-sm font-medium text-text-main">Faculty</label>
                <input id="faculty" name="faculty" maxlength="100" value="{{ old('faculty', $product->faculty) }}" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('faculty')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="category" class="mb-2 block text-sm font-medium text-text-main">Category</label>
                <textarea id="category" name="category" rows="2" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old('category', $product->category) }}</textarea>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <div class="h-px bg-divider-subtle/30"></div>

    <section>
        <h2 class="text-lg font-bold text-text-main">Status & Next Action</h2>
        <p class="mt-1 text-sm text-divider-subtle">Keep the development status and recommended action current.</p>
        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-text-main">Status</label>
                <input id="status" name="status" maxlength="100" value="{{ old('status', $product->status) }}" placeholder="e.g. In-Development, Stagnant, Commercial" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="action" class="mb-2 block text-sm font-medium text-text-main">Action</label>
                <input id="action" name="action" maxlength="150" value="{{ old('action', $product->action) }}" placeholder="e.g. Prototype Stage" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('action')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="abstract" class="mb-2 block text-sm font-medium text-text-main">Abstract / Description</label>
                <textarea id="abstract" name="abstract" rows="5" class="w-full rounded-lg border border-divider-subtle/40 px-4 py-3 text-sm text-text-main outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old('abstract', $product->abstract) }}</textarea>
                @error('abstract')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <div class="flex flex-wrap justify-end gap-3 border-t border-divider-subtle/30 pt-5">
        <a href="{{ route('admin.products.records') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-divider-subtle/40 px-5 text-sm font-bold text-text-main transition hover:bg-background-light">Cancel</a>
        <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary/90">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">save</span> Save Product
        </button>
    </div>
</div>
