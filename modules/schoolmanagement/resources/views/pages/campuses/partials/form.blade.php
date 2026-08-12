<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <h3 class="text-lg font-medium text-gray-900 dark:text-white pb-1 border-b border-gray-200 dark:border-gray-700 col-span-2">
        {{ bi('Campus Details') }}
    </h3>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="bi('Campus Name')"
            :value="old('name', $campus?->name)"
            :placeholder="__('e.g. Main Campus')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="code"
            :label="bi('Campus Code')"
            :value="old('code', $campus?->code)"
            :placeholder="__('e.g. MAIN')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="email"
            name="email"
            :label="bi('Email')"
            :value="old('email', $campus?->email)"
            :placeholder="__('campus@school.edu')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="phone"
            :label="bi('Phone')"
            :value="old('phone', $campus?->phone)"
            :placeholder="__('e.g. +1-555-0100')"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="address"
            :label="bi('Address')"
            :value="old('address', $campus?->address)"
            :placeholder="__('Full address of the campus')"
            :rows="3"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="manager_id"
            :label="bi('Campus Manager')"
            :options="['' => bi('-- Select Manager --')] + ($managers ?? [])"
            :value="old('manager_id', $campus?->manager_id)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="status"
            :label="bi('Status')"
            :options="['active' => bi('Active'), 'inactive' => bi('Inactive')]"
            :value="old('status', $campus?->status ?? 'active')"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons cancelUrl="{{ route('school.campuses.index') }}" />
    </div>

</div>
