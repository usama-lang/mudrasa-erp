<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Step 1: Student Search --}}
        <div class="lg:col-span-1">
            <x-card>
                <x-slot name="title">{{ bi('Search Student') }}</x-slot>
                <div class="relative">
                    <input
                        type="text"
                        id="student-search"
                        class="form-control w-full"
                        placeholder="{{ bi('Search by name or admission no...') }}"
                        autocomplete="off"
                    >
                    <div id="student-results" class="absolute z-20 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-80 overflow-y-auto hidden"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ bi('Search by name or admission no...') }}</p>
            </x-card>

            <div id="student-info" class="mt-6 hidden">
                <x-card>
                    <x-slot name="title">{{ bi('Student Information') }}</x-slot>
                    <dl class="grid grid-cols-1 gap-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">{{ bi('Student') }}</dt><dd id="si-name" class="font-semibold text-gray-900 dark:text-white"></dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ bi('Admission No.') }}</dt><dd id="si-admission" class="font-mono text-gray-900 dark:text-white"></dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ bi('Campus') }}</dt><dd id="si-campus" class="text-gray-900 dark:text-white"></dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ bi('Class') }}</dt><dd id="si-class" class="text-gray-900 dark:text-white"></dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ bi('Monthly Fee') }}</dt><dd id="si-fee" class="text-gray-900 dark:text-white"></dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ bi('Food Charges') }}</dt><dd id="si-food" class="text-gray-900 dark:text-white"></dd></div>
                    </dl>
                </x-card>
            </div>
        </div>

        {{-- Step 2: Fee Entry --}}
        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="title">{{ bi('Fee Collection') }}</x-slot>

                <div id="fee-form-empty" class="text-center py-16 text-gray-400">
                    <iconify-icon icon="lucide:user-search" class="text-4xl mb-2" width="48" height="48"></iconify-icon>
                    <p>{{ bi('No student selected') }}</p>
                </div>

                <form id="fee-form" action="{{ route('school.fees.store') }}" method="POST" class="hidden" data-prevent-unsaved-changes>
                    @csrf
                    <input type="hidden" name="student_id" id="student_id">

                    @if($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm text-red-700 dark:text-red-300">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="fee_month">{{ bi('Fee Month') }} <span class="text-red-500">*</span></label>
                            <select name="fee_month" id="fee_month" class="form-control" required>
                                <option value="" disabled selected>{{ bi('Select a month') }}</option>
                            </select>
                            <p id="no-pending" class="text-xs text-amber-600 mt-1 hidden">{{ bi('No pending months') }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="amount_paid">{{ bi('Amount Paid') }} <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" class="form-control" required>
                        </div>

                        <div>
                            <label class="form-label" for="discount">{{ bi('Discount') }}</label>
                            <input type="number" step="0.01" min="0" name="discount" id="discount" class="form-control" value="0">
                        </div>

                        <div>
                            <label class="form-label" for="payment_method">{{ bi('Payment Method') }} <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="cash">{{ bi('Cash') }}</option>
                                <option value="bank_transfer">{{ bi('Bank Transfer') }}</option>
                                <option value="cheque">{{ bi('Cheque') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" for="payment_date">{{ bi('Payment Date') }}</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ now()->toDateString() }}">
                        </div>

                        <div class="flex flex-col justify-end">
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">
                                <p class="text-xs text-gray-500">{{ bi('Balance') }}</p>
                                <p id="balance-display" class="text-xl font-bold text-gray-900 dark:text-white">PKR 0</p>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label" for="notes">{{ bi('Description') }}</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control-textarea" maxlength="500">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-6">
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="lucide:printer"></iconify-icon> {{ bi('Collect & Print Receipt') }}
                        </button>
                        <a href="{{ route('school.fees.index') }}" wire:navigate class="btn btn-default">{{ bi('Back to List') }}</a>
                    </div>
                </form>
            </x-card>
        </div>

    </div>

    @push('scripts')
    <script>
    (function () {
        const searchInput   = document.getElementById('student-search');
        const resultsBox    = document.getElementById('student-results');
        const infoBox       = document.getElementById('student-info');
        const feeForm       = document.getElementById('fee-form');
        const feeEmpty      = document.getElementById('fee-form-empty');
        const monthSelect   = document.getElementById('fee_month');
        const amountInput   = document.getElementById('amount_paid');
        const discountInput = document.getElementById('discount');
        const balanceLabel  = document.getElementById('balance-display');
        const noPending     = document.getElementById('no-pending');

        const searchUrl = "{{ route('school.fees.search-student') }}";
        let totalDue = 0;
        let debounce;

        function fmt(n) {
            return 'PKR ' + Number(n || 0).toLocaleString();
        }

        function recalcBalance() {
            const paid = parseFloat(amountInput.value) || 0;
            const disc = parseFloat(discountInput.value) || 0;
            const balance = Math.max(totalDue - disc - paid, 0);
            balanceLabel.textContent = fmt(balance);
        }

        function selectStudent(student) {
            document.getElementById('student_id').value = student.id;
            document.getElementById('si-name').textContent = student.student_name;
            document.getElementById('si-admission').textContent = student.admission_no;
            document.getElementById('si-campus').textContent = student.campus_name;
            document.getElementById('si-class').textContent = student.class_name + (student.section_name && student.section_name !== '-' ? ' - ' + student.section_name : '');

            const food = (student.residential_status === 'non_resident' && student.food_at_madrasa) ? student.food_charges : 0;
            document.getElementById('si-fee').textContent = fmt(student.monthly_fee);
            document.getElementById('si-food').textContent = fmt(food);

            totalDue = (parseFloat(student.monthly_fee) || 0) + (parseFloat(food) || 0);
            amountInput.value = totalDue.toFixed(2);

            // Populate pending months.
            monthSelect.innerHTML = '<option value="" disabled>{{ bi('Select a month') }}</option>';
            if (student.pending_months && student.pending_months.length) {
                student.pending_months.forEach(function (m, idx) {
                    const opt = document.createElement('option');
                    opt.value = m.value;
                    opt.textContent = m.label;
                    if (idx === 0) opt.selected = true;
                    monthSelect.appendChild(opt);
                });
                noPending.classList.add('hidden');
            } else {
                noPending.classList.remove('hidden');
            }

            infoBox.classList.remove('hidden');
            feeForm.classList.remove('hidden');
            feeEmpty.classList.add('hidden');
            resultsBox.classList.add('hidden');
            searchInput.value = student.student_name;
            recalcBalance();
        }

        function renderResults(students) {
            resultsBox.innerHTML = '';
            if (!students.length) {
                resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">{{ bi('No students found') }}</div>';
                resultsBox.classList.remove('hidden');
                return;
            }
            students.forEach(function (s) {
                const row = document.createElement('button');
                row.type = 'button';
                row.className = 'w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700';
                row.innerHTML = '<div class="font-medium text-gray-900 dark:text-white">' + s.student_name + '</div>' +
                                '<div class="text-xs text-gray-500">' + s.admission_no + ' · ' + s.class_name + '</div>';
                row.addEventListener('click', function () { selectStudent(s); });
                resultsBox.appendChild(row);
            });
            resultsBox.classList.remove('hidden');
        }

        searchInput.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(debounce);
            if (q.length < 1) { resultsBox.classList.add('hidden'); return; }
            resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">{{ bi('Searching...') }}</div>';
            resultsBox.classList.remove('hidden');
            debounce = setTimeout(function () {
                fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(renderResults)
                    .catch(function () { resultsBox.classList.add('hidden'); });
            }, 250);
        });

        document.addEventListener('click', function (e) {
            if (!resultsBox.contains(e.target) && e.target !== searchInput) {
                resultsBox.classList.add('hidden');
            }
        });

        amountInput.addEventListener('input', recalcBalance);
        discountInput.addEventListener('input', recalcBalance);
    })();
    </script>
    @endpush

</x-layouts.backend-layout>
