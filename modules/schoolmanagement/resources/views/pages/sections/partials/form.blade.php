<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="campus_id"
            :label="bi('Campus')"
            :options="['' => bi('-- Select Campus --')] + ($campuses ?? [])"
            :value="old('campus_id', $section?->campus_id)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="class_id"
            :label="bi('Class')"
            :options="['' => bi('-- Select Class --')] + ($classes ?? [])"
            :value="old('class_id', $section?->class_id)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="bi('Section Name')"
            :value="old('name', $section?->name)"
            :placeholder="__('e.g. Section A')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            type="number"
            name="capacity"
            :label="bi('Capacity')"
            :value="old('capacity', $section?->capacity ?? 30)"
            :placeholder="__('30')"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="status"
            :label="bi('Status')"
            :options="['active' => bi('Active'), 'inactive' => bi('Inactive')]"
            :value="old('status', $section?->status ?? 'active')"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons cancelUrl="{{ route('school.sections.index') }}" />
    </div>

</div>
