<form action="{{ $action }}" method="POST" class="mx-auto max-w-6xl overflow-hidden rounded-xl border border-divider-subtle/30 bg-white shadow-sm">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    @if($errors->any())
        <div class="m-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <p class="font-bold">Please correct the following fields:</p>
            <ul class="mt-1 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @php
        $fieldClass = 'mt-2 h-11 w-full rounded-lg border border-divider-subtle/40 bg-white px-3 text-sm text-text-main focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
        $textFields = [
            ['category', 'Portfolio Category', 'select', $categories],
            ['type', 'IP Type', 'select', $types],
            ['title', 'Product / IP Name', 'text'],
            ['nama_produk_baru', 'New Product Name', 'text'],
            ['ip_number', 'Filing Number', 'text'],
            ['status', 'Status', 'text'],
            ['bil', 'Record Number', 'text'],
            ['tahun', 'Year', 'number'],
            ['tahun_aktiviti', 'Activity Year', 'number'],
            ['fakulti', 'Faculty', 'text'],
            ['coe', 'Centre of Excellence (CoE)', 'text'],
            ['jawatan', 'Position', 'text'],
            ['no_staf', 'Staff ID', 'text'],
            ['gelaran', 'Researcher Title', 'text'],
            ['nama_penyelidik', 'Researcher Name', 'text'],
            ['penyelidik_bersama', 'Co-researchers / Contribution', 'textarea'],
            ['geran', 'Grant', 'text'],
            ['ejen', 'Agent', 'text'],
            ['pewartaan', 'Gazette / Publication', 'text'],
            ['kategori', 'Subcategory', 'text'],
            ['kos', 'Filing Cost (RM)', 'number'],
            ['kos_renewal', 'Renewal Cost (RM)', 'number'],
            ['country', 'Country', 'text'],
            ['link_to_evidence', 'Evidence URL', 'url'],
            ['abstrak', 'Abstract', 'textarea'],
            ['remarks', 'Remarks', 'textarea'],
        ];
        $dateFields = [
            ['filing_date', 'Filing Date'],
            ['grant_date', 'Grant Date'],
            ['expiry_date', 'Expiry Date'],
            ['tarikh_notis', 'Notice Date'],
            ['tarikh_pewartaan', 'Gazette Date'],
            ['tarikh_renewal_terakhir', 'Last Renewal Date'],
            ['tarikh_renewal_seterusnya', 'Next Renewal Date'],
        ];
    @endphp
    <div class="space-y-8 p-5 sm:p-8">
        <div>
            <h2 class="text-lg font-bold text-text-main">Record Details</h2>
            <p class="mt-1 text-sm text-divider-subtle">Core identifiers and filing classification.</p>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach(array_slice($textFields, 0, 7) as $field)
                    @php
                        [$name, $label, $inputType] = $field;
                        $options = $field[3] ?? [];
                    @endphp
                    <div class="{{ in_array($name, ['title', 'nama_produk_baru']) ? 'sm:col-span-2' : '' }}">
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}{{ in_array($name, ['category', 'type', 'title']) ? ' *' : '' }}</label>
                        @if($inputType === 'select')
                            <select id="ip-{{ $name }}" name="{{ $name }}" class="{{ $fieldClass }}" @required(in_array($name, ['category', 'type']))>
                                <option value="">Select {{ $label }}</option>
                                @foreach($options as $option)
                                    <option value="{{ $option }}" @selected(old($name, $ip->{$name}) === $option)>{{ str_replace('_', ' ', $option) }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="ip-{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name, $ip->{$name}) }}" class="{{ $fieldClass }}" @required($name === 'title') @if($inputType === 'number') min="1900" max="2100" @endif>
                        @endif
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-divider-subtle/20 pt-7">
            <h2 class="text-lg font-bold text-text-main">Status, Costs & Dates</h2>
            <p class="mt-1 text-sm text-divider-subtle">Track filing activity and protection renewal dates.</p>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach(array_slice($textFields, 7, 2) as [$name, $label, $inputType])
                    <div>
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}</label>
                        <input id="ip-{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name, $ip->{$name}) }}" class="{{ $fieldClass }}" @if($inputType === 'number') min="1900" max="2100" @endif>
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                @foreach($dateFields as [$name, $label])
                    <div>
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}</label>
                        <input id="ip-{{ $name }}" name="{{ $name }}" type="date" value="{{ old($name, $ip->{$name}?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                @foreach(array_slice($textFields, 19, 1) as [$name, $label, $inputType])
                    <div>
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}</label>
                        <input id="ip-{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name, $ip->{$name}) }}" class="{{ $fieldClass }}">
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                @foreach(array_slice($textFields, 20, 2) as [$name, $label, $inputType])
                    <div>
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}</label>
                        <input id="ip-{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name, $ip->{$name}) }}" step="0.01" min="0" class="{{ $fieldClass }}">
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-divider-subtle/20 pt-7">
            <h2 class="text-lg font-bold text-text-main">Research & Links</h2>
            <p class="mt-1 text-sm text-divider-subtle">Associate this record with researchers, project information, and supporting documents.</p>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach(array_slice($textFields, 9, 10) as [$name, $label, $inputType])
                    <div class="{{ in_array($name, ['penyelidik_bersama']) ? 'sm:col-span-2 xl:col-span-3' : '' }}">
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}</label>
                        @if($inputType === 'textarea')
                            <textarea id="ip-{{ $name }}" name="{{ $name }}" rows="3" class="{{ str_replace('h-11 ', '', $fieldClass) }} py-3">{{ old($name, $ip->{$name}) }}</textarea>
                        @else
                            <input id="ip-{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name, $ip->{$name}) }}" class="{{ $fieldClass }}">
                        @endif
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach

                <div>
                    <label for="ip-staff-ids" class="block text-sm font-medium text-text-main">Linked RICM Staff</label>
                    <select id="ip-staff-ids" name="staff_ids[]" multiple class="{{ str_replace('h-11 ', '', $fieldClass) }} min-h-32 py-3">
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" @selected(in_array($member->id, old('staff_ids', $ip->exists ? $ip->staff->pluck('id')->all() : [])))>
                                {{ $member->staff_id }} - {{ $member->staff_name }}{{ $member->faculty?->faculty_code ? ' ('.$member->faculty->faculty_code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-divider-subtle">Hold Ctrl/Cmd to select multiple staff members.</p>
                    @error('staff_ids')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="ip-project-id" class="block text-sm font-medium text-text-main">Linked Project</label>
                    <select id="ip-project-id" name="project_id" class="{{ $fieldClass }}">
                        <option value="">No linked project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected((string) old('project_id', $ip->project_id) === (string) $project->id)>
                                {{ $project->project_id }} - {{ Str::limit($project->project_title, 70) }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @foreach(array_slice($textFields, 22) as [$name, $label, $inputType])
                    <div class="{{ in_array($name, ['abstrak', 'remarks']) ? 'sm:col-span-2 xl:col-span-3' : '' }}">
                        <label for="ip-{{ $name }}" class="block text-sm font-medium text-text-main">{{ $label }}</label>
                        @if($inputType === 'textarea')
                            <textarea id="ip-{{ $name }}" name="{{ $name }}" rows="4" class="{{ str_replace('h-11 ', '', $fieldClass) }} py-3">{{ old($name, $ip->{$name}) }}</textarea>
                        @else
                            <input id="ip-{{ $name }}" name="{{ $name }}" type="{{ $inputType }}" value="{{ old($name, $ip->{$name}) }}" class="{{ $fieldClass }}">
                        @endif
                        @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="flex flex-wrap justify-end gap-3 border-t border-divider-subtle/20 bg-background-light px-5 py-4 sm:px-8">
        <a href="{{ route('admin.ip.index') }}" class="inline-flex h-11 items-center rounded-lg border border-divider-subtle/40 bg-white px-5 text-sm font-bold text-text-main transition hover:bg-background-light">Cancel</a>
        <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-lg bg-primary px-6 text-sm font-bold text-white transition hover:bg-primary/90">
            <span class="material-symbols-outlined text-lg" aria-hidden="true">save</span> {{ $method === 'POST' ? 'Create Record' : 'Save Changes' }}
        </button>
    </div>
</form>
