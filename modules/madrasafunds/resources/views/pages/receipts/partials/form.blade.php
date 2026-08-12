@php
    $fundsJson = $funds->mapWithKeys(fn ($f) => [$f->id => $f->type?->value ?? 'donation'])->toJson();
@endphp

@if($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm text-red-700 dark:text-red-300">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="mf-receipt-form-grid" data-fund-types='{{ $fundsJson }}'>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="fund_id"
            id="mf-fund-id"
            :label="mf_bi('Fund')"
            :options="['' => mf_bi('-- Select Fund --')] + $funds->pluck('name', 'id')->toArray()"
            :value="old('fund_id', $receipt?->fund_id)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="receipt_date"
            type="date"
            :label="mf_bi('Receipt Date')"
            :value="old('receipt_date', optional($receipt?->receipt_date)->format('Y-m-d') ?? now()->toDateString())"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="department_id"
            :label="mf_bi('Department')"
            :options="['' => mf_bi('-- Select Department --')] + $departments->pluck('name', 'id')->toArray()"
            :value="old('department_id', $receipt?->department_id)"
        />
    </div>

    {{-- Student autocomplete — only relevant for fee-type funds. --}}
    <div class="col-span-2 md:col-span-1 mf-fees-only">
        <label class="form-label" for="mf-student-search">{{ mf_bi('Student') }}</label>
        <div class="relative">
            <input type="text" id="mf-student-search" class="form-control w-full" autocomplete="off"
                placeholder="{{ mf_bi('Search by name or class...') }}"
                value="{{ old('student_id') ? optional($receipt?->student)->name : optional($receipt?->student)->name }}">
            <input type="hidden" name="student_id" id="mf-student-id" value="{{ old('student_id', $receipt?->student_id) }}">
            <div id="mf-student-results" class="absolute z-20 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-64 overflow-y-auto hidden"></div>
        </div>
    </div>

    {{-- Donor fields — only relevant for donation/trust funds. --}}
    <div class="col-span-2 md:col-span-1 mf-donor-only">
        <x-inputs.input
            name="donor_name"
            :label="mf_bi('Donor Name')"
            :value="old('donor_name', $receipt?->donor_name)"
        />
    </div>

    <div class="col-span-2 md:col-span-1 mf-donor-only">
        <x-inputs.input
            name="donor_phone"
            :label="mf_bi('Donor Phone')"
            :value="old('donor_phone', $receipt?->donor_phone)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="amount"
            type="number"
            step="0.01"
            min="0.01"
            :label="mf_bi('Amount')"
            :value="old('amount', $receipt?->amount)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="mad"
            :label="mf_bi('Head (Mad)')"
            :value="old('mad', $receipt?->mad)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="given_to"
            :label="mf_bi('Given To')"
            :value="old('given_to', $receipt?->given_to)"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="description"
            :label="mf_bi('Description')"
            :value="old('description', $receipt?->description)"
            :rows="2"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="notes"
            :label="mf_bi('Notes')"
            :value="old('notes', $receipt?->notes)"
            :rows="2"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons :cancelUrl="route('admin.madrasafunds.receipts.index')" />
    </div>

</div>

@push('scripts')
<script>
(function () {
    const grid        = document.getElementById('mf-receipt-form-grid');
    const fundSelect  = document.getElementById('mf-fund-id');
    const fundTypes   = JSON.parse(grid.dataset.fundTypes || '{}');
    const feesEls     = document.querySelectorAll('.mf-fees-only');
    const donorEls    = document.querySelectorAll('.mf-donor-only');
    const searchInput = document.getElementById('mf-student-search');
    const studentId   = document.getElementById('mf-student-id');
    const resultsBox  = document.getElementById('mf-student-results');
    const searchUrl   = "{{ route('admin.madrasafunds.receipts.search-student') }}";
    let debounce;

    function show(els, visible) {
        els.forEach(el => el.classList.toggle('hidden', !visible));
    }

    function applyFundType() {
        const type = fundTypes[fundSelect.value] || null;
        const isFees = type === 'fees';
        show(feesEls, isFees);
        show(donorEls, !isFees);
    }

    fundSelect.addEventListener('change', applyFundType);
    applyFundType();

    searchInput?.addEventListener('input', function () {
        const q = this.value.trim();
        studentId.value = '';
        clearTimeout(debounce);
        if (q.length < 1) { resultsBox.classList.add('hidden'); return; }
        resultsBox.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">{{ mf_bi('Searching...') }}</div>';
        resultsBox.classList.remove('hidden');
        debounce = setTimeout(function () {
            fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(function (students) {
                    resultsBox.innerHTML = '';
                    if (!students.length) {
                        resultsBox.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">{{ mf_bi('No students found') }}</div>';
                        return;
                    }
                    students.forEach(function (s) {
                        const row = document.createElement('button');
                        row.type = 'button';
                        row.className = 'w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700';
                        row.innerHTML = '<div class="font-medium text-gray-900 dark:text-white">' + s.name + '</div>' +
                                        '<div class="text-xs text-gray-500">' + s.class + ' · ' + s.department_name + '</div>';
                        row.addEventListener('click', function () {
                            studentId.value = s.id;
                            searchInput.value = s.name;
                            resultsBox.classList.add('hidden');
                        });
                        resultsBox.appendChild(row);
                    });
                })
                .catch(() => resultsBox.classList.add('hidden'));
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (resultsBox && !resultsBox.contains(e.target) && e.target !== searchInput) {
            resultsBox.classList.add('hidden');
        }
    });
})();
</script>
@endpush
