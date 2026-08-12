<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="mf_bi('Department Name')"
            :value="old('name', $department?->name)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name_urdu"
            :label="mf_bi('Name (Urdu)')"
            :value="old('name_urdu', $department?->name_urdu)"
            dir="rtl"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="description"
            :label="mf_bi('Description')"
            :value="old('description', $department?->description)"
            :rows="3"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.toggle
            name="is_active"
            :label="mf_bi('Active')"
            :checked="old('is_active', $department?->is_active ?? true)"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons :cancelUrl="route('admin.madrasafunds.departments.index')" />
    </div>

</div>
