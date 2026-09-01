<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="{{ $prefix }}staff_input" class="block text-sm font-medium text-text-main mb-2">
            Staff <span class="text-red-500">*</span>
        </label>
        <input type="text" id="{{ $prefix }}staff_input" data-entity="staff" data-target="{{ $prefix }}staff_id"
            list="staff-options" autocomplete="off" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
            placeholder="Type staff ID or name">
        <input type="hidden" id="{{ $prefix }}staff_id" name="staff_id">
    </div>

    <div>
        <label for="{{ $prefix }}project_input" class="block text-sm font-medium text-text-main mb-2">
            Project <span class="text-red-500">*</span>
        </label>
        <input type="text" id="{{ $prefix }}project_input" data-entity="project" data-target="{{ $prefix }}project_id"
            list="project-options" autocomplete="off" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
            placeholder="Type project code or title">
        <input type="hidden" id="{{ $prefix }}project_id" name="project_id">
    </div>

    <div>
        <label for="{{ $prefix }}event_input" class="block text-sm font-medium text-text-main mb-2">
            Event <span class="text-red-500">*</span>
        </label>
        <input type="text" id="{{ $prefix }}event_input" data-entity="event" data-target="{{ $prefix }}event_id"
            list="event-options" autocomplete="off" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
            placeholder="Type event name">
        <input type="hidden" id="{{ $prefix }}event_id" name="event_id">
    </div>

    <div>
        <label for="{{ $prefix }}award_name" class="block text-sm font-medium text-text-main mb-2">
            Award Name <span class="text-red-500">*</span>
        </label>
        <select id="{{ $prefix }}award_name" name="award_name" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
            <option value="">Select Award</option>
            <option value="PLATINUM">Platinum</option>
            <option value="SPECIAL">Special</option>
            <option value="GOLD">Gold</option>
            <option value="SILVER">Silver</option>
            <option value="BRONZE">Bronze</option>
            <option value="OTHERS">Others</option>
        </select>
    </div>

    <div>
        <label for="{{ $prefix }}award_level" class="block text-sm font-medium text-text-main mb-2">
            Award Level <span class="text-red-500">*</span>
        </label>
        <select id="{{ $prefix }}award_level" name="award_level" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
            <option value="">Select Level</option>
            <option value="INDIVIDUAL">Individual</option>
            <option value="INSTITUTIONAL">Institutional</option>
        </select>
    </div>

    <div>
        <label for="{{ $prefix }}award_type" class="block text-sm font-medium text-text-main mb-2">
            Award Type <span class="text-red-500">*</span>
        </label>
        <select id="{{ $prefix }}award_type" name="award_type" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
            <option value="">Select Type</option>
            <option value="AWARD">Award</option>
            <option value="RECOGNITION">Recognition</option>
            <option value="STEWARDSHIP">Stewardship</option>
            <option value="EXHIBITION">Exhibition</option>
            <option value="OTHER RESEARCH AWARDS">Other Research Awards</option>
            <option value="CLARIVATE HIGHLY AWARD">Clarivate Highly Award</option>
        </select>
    </div>

    <div>
        <label for="{{ $prefix }}event_exhibition_level" class="block text-sm font-medium text-text-main mb-2">
            Exhibition Level <span class="text-red-500">*</span>
        </label>
        <select id="{{ $prefix }}event_exhibition_level" name="event_exhibition_level" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
            <option value="">Select Level</option>
            <option value="NATIONAL">National</option>
            <option value="INTERNATIONAL">International</option>
        </select>
    </div>

    <div>
        <label for="{{ $prefix }}award_date" class="block text-sm font-medium text-text-main mb-2">
            Award Date <span class="text-red-500">*</span>
        </label>
        <input type="date" id="{{ $prefix }}award_date" name="award_date" required
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
    </div>

    <div class="md:col-span-2">
        <label for="{{ $prefix }}evidence_document" class="block text-sm font-medium text-text-main mb-2">
            Evidence Document
        </label>
        <input type="file" id="{{ $prefix }}evidence_document" name="evidence_document" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
            class="w-full px-4 py-3 border border-divider-subtle/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
        <p class="text-xs text-divider-subtle mt-2">
            Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP (Max 10MB)
        </p>
    </div>
</div>
