<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    @if(!$teacher)
    <h3 class="text-lg font-medium text-gray-900 dark:text-white pb-1 border-b border-gray-200 dark:border-gray-700 col-span-2">
        {{ bi('Account Information') }}
    </h3>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="first_name"
            :label="bi('First Name')"
            :value="old('first_name')"
            :placeholder="__('Enter First Name')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="last_name"
            :label="bi('Last Name')"
            :value="old('last_name')"
            :placeholder="__('Enter Last Name')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="email"
            name="email"
            :label="bi('Email')"
            :value="old('email')"
            :placeholder="__('teacher@school.edu')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.password
            name="password"
            :label="bi('Password')"
            :placeholder="__('Enter Password')"
            :required="true"
            :autogenerate="true"
        />
    </div>
    @else
    <h3 class="text-lg font-medium text-gray-900 dark:text-white pb-1 border-b border-gray-200 dark:border-gray-700 col-span-2">
        {{ bi('Account Information') }}
    </h3>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="first_name"
            :label="bi('First Name')"
            :value="old('first_name', $teacher->user?->first_name)"
            :placeholder="__('Enter First Name')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="last_name"
            :label="bi('Last Name')"
            :value="old('last_name', $teacher->user?->last_name)"
            :placeholder="__('Enter Last Name')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="email"
            name="email"
            :label="bi('Email')"
            :value="old('email', $teacher->user?->email)"
            :placeholder="__('teacher@school.edu')"
            required
        />
    </div>
    @endif

    <h3 class="text-lg font-medium text-gray-900 dark:text-white pb-1 border-b border-gray-200 dark:border-gray-700 col-span-2">
        {{ bi('Employment Details') }}
    </h3>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="campus_id"
            :label="bi('Campus')"
            :options="['' => bi('-- Select Campus --')] + ($campuses ?? [])"
            :value="old('campus_id', $teacher?->campus_id)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="employee_id"
            :label="bi('Employee ID')"
            :value="old('employee_id', $teacher?->employee_id ?? ($employeeId ?? ''))"
            :placeholder="__('EMP-00001')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="designation"
            :label="bi('Designation')"
            :value="old('designation', $teacher?->designation)"
            :placeholder="__('e.g. Senior Teacher')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="qualification"
            :label="bi('Qualification')"
            :value="old('qualification', $teacher?->qualification)"
            :placeholder="__('e.g. M.Sc. Computer Science')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="number"
            name="salary"
            :label="bi('Monthly Salary')"
            :value="old('salary', $teacher?->salary)"
            :placeholder="__('0.00')"
            step="0.01"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="date"
            name="joining_date"
            :label="bi('Joining Date')"
            :value="old('joining_date', $teacher?->joining_date?->format('Y-m-d'))"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="status"
            :label="bi('Status')"
            :options="['active' => bi('Active'), 'inactive' => bi('Inactive')]"
            :value="old('status', $teacher?->status ?? 'active')"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons cancelUrl="{{ route('school.teachers.index') }}" />
    </div>

</div>
