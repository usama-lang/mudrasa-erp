{{--
    Shared report filter bar.
    Expects: $action (route), $filters (array), $funds, $departments, $fundTypes
    Pass $show as an array of which filters to render, e.g. ['fund','department','dates']
--}}
@php $show = $show ?? ['fund', 'fund_type', 'department', 'dates']; @endphp

<form method="GET" action="{{ $action }}" class="mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

        @if(in_array('fund', $show))
        <div>
            <label class="form-label" for="fund_id">{{ mf_bi('Fund') }}</label>
            <select name="fund_id" id="fund_id" class="form-control text-sm">
                <option value="">{{ mf_bi('All') }}</option>
                @foreach($funds as $fund)
                    <option value="{{ $fund->id }}" @selected(($filters['fund_id'] ?? null) == $fund->id)>{{ $fund->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(in_array('fund_type', $show))
        <div>
            <label class="form-label" for="fund_type">{{ mf_bi('Fund Type') }}</label>
            <select name="fund_type" id="fund_type" class="form-control text-sm">
                <option value="">{{ mf_bi('All') }}</option>
                @foreach($fundTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['fund_type'] ?? null) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(in_array('department', $show))
        <div>
            <label class="form-label" for="department_id">{{ mf_bi('Department') }}</label>
            <select name="department_id" id="department_id" class="form-control text-sm">
                <option value="">{{ mf_bi('All Departments') }}</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? null) == $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(in_array('dates', $show))
        <div>
            <label class="form-label" for="date_from">{{ mf_bi('Date From') }}</label>
            <input type="date" name="date_from" id="date_from" class="form-control text-sm" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div>
            <label class="form-label" for="date_to">{{ mf_bi('Date To') }}</label>
            <input type="date" name="date_to" id="date_to" class="form-control text-sm" value="{{ $filters['date_to'] ?? '' }}">
        </div>
        @endif

        <div>
            <button type="submit" class="btn btn-primary w-full">
                <iconify-icon icon="lucide:filter"></iconify-icon> {{ mf_bi('Generate Report') }}
            </button>
        </div>
    </div>
</form>
