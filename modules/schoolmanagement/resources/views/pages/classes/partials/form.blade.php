<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="campus_id"
            :label="bi('Campus')"
            :options="['' => bi('-- Select Campus --')] + ($campuses ?? [])"
            :value="old('campus_id', $schoolClass?->campus_id)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="department_id"
            :label="bi('Department')"
            :options="['' => bi('-- Select Department --')] + ($departments ?? [])"
            :value="old('department_id', $schoolClass?->department_id)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="bi('Class Name')"
            :value="old('name', $schoolClass?->name)"
            :placeholder="__('e.g. Class 10-A')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="number"
            name="numeric_name"
            :label="bi('Class Level (Number)')"
            :value="old('numeric_name', $schoolClass?->numeric_name)"
            :placeholder="__('e.g. 10')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="status"
            :label="bi('Status')"
            :options="['active' => bi('Active'), 'inactive' => bi('Inactive')]"
            :value="old('status', $schoolClass?->status ?? 'active')"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons cancelUrl="{{ route('school.classes.index') }}" />
    </div>

</div>
