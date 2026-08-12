<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="campus_id"
            :label="bi('Campus')"
            :options="['' => bi('-- Select Campus --')] + ($campuses ?? [])"
            :value="old('campus_id', $department?->campus_id)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="bi('Department Name')"
            :value="old('name', $department?->name)"
            :placeholder="__('e.g. Computer Science')"
            required
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="description"
            :label="bi('Description')"
            :value="old('description', $department?->description)"
            :placeholder="__('Brief description of this department')"
            :rows="3"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="status"
            :label="bi('Status')"
            :options="['active' => bi('Active'), 'inactive' => bi('Inactive')]"
            :value="old('status', $department?->status ?? 'active')"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons cancelUrl="{{ route('school.departments.index') }}" />
    </div>

</div>
