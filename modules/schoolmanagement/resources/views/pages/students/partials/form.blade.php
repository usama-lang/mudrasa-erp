<div class="space-y-6">

    {{-- Account Information (optional) --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Account Information') }}
            <span class="text-xs font-normal text-gray-500 ml-2">{{ bi('(Optional — leave blank to skip login creation)') }}</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.input
                    type="email"
                    name="email"
                    :label="bi('Email')"
                    :value="old('email', $student?->user?->email)"
                    :placeholder="__('student@school.edu')"
                />
            </div>
            @if(!$student)
            <div>
                <x-inputs.password
                    name="password"
                    :label="bi('Password')"
                    :placeholder="__('Enter Password')"
                    :autogenerate="true"
                />
            </div>
            @endif
        </div>
    </div>

    {{-- Personal Details --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Personal Details') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.input
                    name="student_name"
                    :label="bi('Student Name')"
                    :value="old('student_name', $student?->student_name)"
                    :placeholder="__('Full name')"
                    required
                />
            </div>
            <div>
                <x-inputs.input
                    name="father_name"
                    :label="bi('Father Name')"
                    :value="old('father_name', $student?->father_name)"
                    :placeholder="__('Father full name')"
                />
            </div>
            <div>
                <x-inputs.input
                    type="date"
                    name="date_of_birth"
                    :label="bi('Date of Birth')"
                    :value="old('date_of_birth', $student?->date_of_birth?->format('Y-m-d'))"
                />
            </div>
            <div>
                <x-inputs.input
                    type="number"
                    name="age"
                    :label="bi('Age')"
                    :value="old('age', $student?->age)"
                    :placeholder="__('Age in years')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="b_form_no"
                    :label="bi('B-Form No.')"
                    :value="old('b_form_no', $student?->b_form_no)"
                    :placeholder="__('Child B-Form number')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="father_cnic"
                    :label="bi('Father CNIC')"
                    :value="old('father_cnic', $student?->father_cnic)"
                    :placeholder="__('e.g. 42101-1234567-1')"
                />
            </div>
        </div>
    </div>

    {{-- Enrollment Details --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Enrollment Details') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.select
                    name="campus_id"
                    :label="bi('Campus')"
                    :options="['' => bi('-- Select Campus --')] + ($campuses ?? [])"
                    :value="old('campus_id', $student?->campus_id)"
                    required
                />
            </div>
            <div>
                <x-inputs.select
                    name="department_id"
                    :label="bi('Department')"
                    :options="['' => bi('-- Select Department --')] + ($departments ?? [])"
                    :value="old('department_id', $student?->department_id)"
                />
            </div>
            <div>
                <x-inputs.select
                    name="class_id"
                    :label="bi('Class')"
                    :options="['' => bi('-- Select Class --')] + ($classes ?? [])"
                    :value="old('class_id', $student?->class_id)"
                />
            </div>
            <div>
                <x-inputs.select
                    name="section_id"
                    :label="bi('Section')"
                    :options="['' => bi('-- Select Section --')] + ($sections ?? [])"
                    :value="old('section_id', $student?->section_id)"
                />
            </div>
            <div>
                <x-inputs.input
                    name="admission_no"
                    :label="bi('Admission No.')"
                    :value="old('admission_no', $student?->admission_no)"
                    :placeholder="__('Auto-generated if blank')"
                />
            </div>
            <div>
                <x-inputs.input
                    type="date"
                    name="admission_date"
                    :label="bi('Admission Date')"
                    :value="old('admission_date', $student?->admission_date?->format('Y-m-d') ?? today()->format('Y-m-d'))"
                    required
                />
            </div>
            <div>
                <x-inputs.input
                    type="number"
                    name="monthly_fee"
                    :label="bi('Monthly Fee (PKR)')"
                    :value="old('monthly_fee', $student?->monthly_fee ?? 0)"
                    :placeholder="__('0.00')"
                />
            </div>
            <div>
                <x-inputs.select
                    name="residential_status"
                    :label="bi('Residential Status')"
                    :options="['non_resident' => bi('Non-Resident'), 'resident' => bi('Resident')]"
                    :value="old('residential_status', $student?->residential_status ?? 'non_resident')"
                />
            </div>

            {{-- Food section — shown only when non_resident --}}
            <div id="food-section" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4" style="display:none">
                <div>
                    <label class="form-label">{{ bi('Food at Madrasa') }}</label>
                    <select name="food_at_madrasa" id="food_at_madrasa" class="form-control">
                        <option value="0" {{ !old('food_at_madrasa', $student?->food_at_madrasa) ? 'selected' : '' }}>{{ bi('No – Brings Own Food') }}</option>
                        <option value="1" {{ old('food_at_madrasa', $student?->food_at_madrasa) ? 'selected' : '' }}>{{ bi('Yes – Eats at Madrasa') }}</option>
                    </select>
                </div>
                <div id="food-charges-section" style="display:none">
                    <x-inputs.input
                        type="number"
                        name="food_charges"
                        :label="bi('Food Charges (PKR)')"
                        :value="old('food_charges', $student?->food_charges ?? 0)"
                        :placeholder="__('0.00')"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Enrollment Status --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Enrollment Status') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.select
                    name="enrollment_status"
                    :label="bi('Enrollment Status')"
                    :options="[
                        'enrolled'   => bi('Enrolled'),
                        'left_self'  => bi('Left (Self)'),
                        'expelled'   => bi('Expelled'),
                        'completed'  => bi('Completed'),
                    ]"
                    :value="old('enrollment_status', $student?->enrollment_status ?? 'enrolled')"
                />
            </div>
            <div id="leaving-date-section" style="display:none">
                <x-inputs.input
                    type="date"
                    name="leaving_date"
                    :label="bi('Leaving Date')"
                    :value="old('leaving_date', $student?->leaving_date?->format('Y-m-d'))"
                />
            </div>
            <div id="status-remarks-section" class="md:col-span-2" style="display:none">
                <x-inputs.textarea
                    name="status_remarks"
                    :label="bi('Status Change Remarks')"
                    :value="old('status_remarks', $student?->status_remarks)"
                    :placeholder="__('Reason for status change (required)')"
                    :rows="3"
                />
                <p class="text-xs text-amber-600 mt-1">{{ bi('Remarks are required when status is not Enrolled.') }}</p>
            </div>
        </div>
    </div>

    {{-- Education & Profession --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Education & Profession') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.input
                    name="education_level"
                    :label="bi('Education Level')"
                    :value="old('education_level', $student?->education_level)"
                    :placeholder="__('e.g. Hifz Year 1')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="designation"
                    :label="bi('Designation / Class (Previous)')"
                    :value="old('designation', $student?->designation)"
                    :placeholder="__('Previous class or rank')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="father_profession"
                    :label="bi('Father Profession')"
                    :value="old('father_profession', $student?->father_profession)"
                    :placeholder="__('Father occupation')"
                />
            </div>
        </div>
    </div>

    {{-- Contact Details --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Contact Details') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.input
                    name="phone_no"
                    :label="bi('Phone No.')"
                    :value="old('phone_no', $student?->phone_no)"
                    :placeholder="__('e.g. 0300-1234567')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="whatsapp_no"
                    :label="bi('WhatsApp No.')"
                    :value="old('whatsapp_no', $student?->whatsapp_no)"
                    :placeholder="__('e.g. 0300-1234567')"
                />
            </div>
            <div class="col-span-2">
                <x-inputs.textarea
                    name="current_address"
                    :label="bi('Current Address')"
                    :value="old('current_address', $student?->current_address)"
                    :placeholder="__('Current residential address')"
                    :rows="2"
                />
            </div>
            <div class="col-span-2">
                <x-inputs.textarea
                    name="permanent_address"
                    :label="bi('Permanent Address')"
                    :value="old('permanent_address', $student?->permanent_address)"
                    :placeholder="__('Permanent home address')"
                    :rows="2"
                />
            </div>
        </div>
    </div>

    {{-- Guardian 1 --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Guardian 1') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-inputs.input
                    name="guardian1_name"
                    :label="bi('Name')"
                    :value="old('guardian1_name', $student?->guardian1_name)"
                    :placeholder="__('Guardian full name')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="guardian1_relation"
                    :label="bi('Relation')"
                    :value="old('guardian1_relation', $student?->guardian1_relation)"
                    :placeholder="__('e.g. Uncle, Brother')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="guardian1_phone"
                    :label="bi('Phone')"
                    :value="old('guardian1_phone', $student?->guardian1_phone)"
                    :placeholder="__('Contact number')"
                />
            </div>
        </div>
    </div>

    {{-- Guardian 2 --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Guardian 2') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-inputs.input
                    name="guardian2_name"
                    :label="bi('Name')"
                    :value="old('guardian2_name', $student?->guardian2_name)"
                    :placeholder="__('Guardian full name')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="guardian2_relation"
                    :label="bi('Relation')"
                    :value="old('guardian2_relation', $student?->guardian2_relation)"
                    :placeholder="__('e.g. Uncle, Neighbour')"
                />
            </div>
            <div>
                <x-inputs.input
                    name="guardian2_phone"
                    :label="bi('Phone')"
                    :value="old('guardian2_phone', $student?->guardian2_phone)"
                    :placeholder="__('Contact number')"
                />
            </div>
        </div>
    </div>

    {{-- Agreement & Documents --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
            {{ bi('Agreement & Documents') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-inputs.input
                    type="date"
                    name="agreement_date"
                    :label="bi('Agreement Date')"
                    :value="old('agreement_date', $student?->agreement_date?->format('Y-m-d'))"
                />
            </div>
            <div>
                <label class="form-label">{{ bi('Status') }}</label>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status', $student?->status ?? true) ? 'selected' : '' }}>{{ bi('Active') }}</option>
                    <option value="0" {{ !old('status', $student?->status ?? true) ? 'selected' : '' }}>{{ bi('Inactive') }}</option>
                </select>
            </div>
            <div>
                <label class="form-label">{{ bi('Student Photo') }}</label>
                <input type="file" name="student_image" class="form-control" accept="image/*">
                @if($student?->student_image)
                <p class="text-xs text-gray-500 mt-1">{{ bi('Current:') }} {{ basename($student->student_image) }}</p>
                @endif
            </div>
            <div>
                <label class="form-label">{{ bi('Document File') }}</label>
                <input type="file" name="document_file" class="form-control">
                @if($student?->document_file)
                <p class="text-xs text-gray-500 mt-1">{{ bi('Current:') }} {{ basename($student->document_file) }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="flex mt-4">
        <x-buttons.submit-buttons cancelUrl="{{ route('school.students.index') }}" />
    </div>

</div>

@push('scripts')
<script>
(function () {
    var residentialSel  = document.getElementById('residential_status');
    var foodSection     = document.getElementById('food-section');
    var foodAtMadrasa   = document.getElementById('food_at_madrasa');
    var foodCharges     = document.getElementById('food-charges-section');
    var enrollmentSel   = document.getElementById('enrollment_status');
    var leavingSection  = document.getElementById('leaving-date-section');
    var remarksSection  = document.getElementById('status-remarks-section');

    function toggleFood() {
        var isNonResident = residentialSel && residentialSel.value === 'non_resident';
        if (foodSection) foodSection.style.display = isNonResident ? '' : 'none';
        if (!isNonResident && foodAtMadrasa) foodAtMadrasa.value = '0';
        toggleFoodCharges();
    }

    function toggleFoodCharges() {
        var eats = foodAtMadrasa && foodAtMadrasa.value === '1';
        if (foodCharges) foodCharges.style.display = eats ? '' : 'none';
    }

    function toggleEnrollmentFields() {
        var status = enrollmentSel ? enrollmentSel.value : 'enrolled';
        var notEnrolled = status !== 'enrolled';
        if (leavingSection) leavingSection.style.display = notEnrolled ? '' : 'none';
        if (remarksSection) remarksSection.style.display = notEnrolled ? '' : 'none';
        var leavingInput  = leavingSection  ? leavingSection.querySelector('input')    : null;
        var remarksInput  = remarksSection  ? remarksSection.querySelector('textarea') : null;
        if (leavingInput)  leavingInput.required  = notEnrolled;
        if (remarksInput)  remarksInput.required  = notEnrolled;
    }

    if (residentialSel) residentialSel.addEventListener('change', toggleFood);
    if (foodAtMadrasa)  foodAtMadrasa.addEventListener('change', toggleFoodCharges);
    if (enrollmentSel)  enrollmentSel.addEventListener('change', toggleEnrollmentFields);

    // init on page load
    toggleFood();
    toggleEnrollmentFields();
})();
</script>
@endpush
