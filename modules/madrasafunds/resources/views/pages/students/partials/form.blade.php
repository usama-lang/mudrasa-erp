<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="mf_bi('Student Name')"
            :value="old('name', $student?->name)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="father_name"
            :label="mf_bi('Father Name')"
            :value="old('father_name', $student?->father_name)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="class"
            :label="mf_bi('Class')"
            :value="old('class', $student?->class)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="department_id"
            :label="mf_bi('Department')"
            :options="['' => mf_bi('-- Select Department --')] + $departments"
            :value="old('department_id', $student?->department_id)"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="phone"
            :label="mf_bi('Phone')"
            :value="old('phone', $student?->phone)"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="address"
            :label="mf_bi('Address')"
            :value="old('address', $student?->address)"
            :rows="2"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.toggle
            name="is_active"
            :label="mf_bi('Active')"
            :checked="old('is_active', $student?->is_active ?? true)"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons :cancelUrl="route('admin.madrasafunds.students.index')" />
    </div>

</div>
