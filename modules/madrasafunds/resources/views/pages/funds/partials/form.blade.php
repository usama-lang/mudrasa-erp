<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name"
            :label="mf_bi('Fund Name')"
            :value="old('name', $fund?->name)"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="name_urdu"
            :label="mf_bi('Name (Urdu)')"
            :value="old('name_urdu', $fund?->name_urdu)"
            dir="rtl"
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.select
            name="type"
            :label="mf_bi('Fund Type')"
            :options="\Modules\MadrasaFunds\Enums\FundType::options()"
            :value="old('type', $fund?->type?->value ?? 'donation')"
            required
        />
    </div>

    <div class="col-span-2 md:col-span-1">
        <x-inputs.input
            name="color"
            type="color"
            :label="mf_bi('Color')"
            :value="old('color', $fund?->color ?? '#635bff')"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.textarea
            name="description"
            :label="mf_bi('Description')"
            :value="old('description', $fund?->description)"
            :rows="3"
        />
    </div>

    <div class="col-span-2">
        <x-inputs.toggle
            name="is_active"
            :label="mf_bi('Active')"
            :checked="old('is_active', $fund?->is_active ?? true)"
        />
    </div>

    <div class="col-span-2 flex mt-4">
        <x-buttons.submit-buttons :cancelUrl="route('admin.madrasafunds.funds.index')" />
    </div>

</div>
