<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @push('styles')
    <style>
        @media print { .sidebar, header, .no-print, nav, aside { display: none !important; } }
    </style>
    @endpush

    <x-card class="mb-6 no-print">
        <x-slot name="title">{{ bi('Class Summary') }}</x-slot>
        <form method="GET" action="{{ route('school.fees.reports.class') }}" class="flex items-end gap-3 flex-wrap">
            <div>
                <label class="form-label">{{ bi('Fee Month') }}</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control">
            </div>
            <div>
                <label class="form-label">{{ bi('Department') }}</label>
                <select name="department_id" class="form-control">
                    <option value="">{{ bi('All Departments') }}</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}" @selected($departmentId == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">{{ bi('Generate Report') }}</button>
            <button type="button" onclick="window.print()" class="btn btn-default">{{ bi('Print') }}</button>
        </form>
    </x-card>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">{{ bi('Class') }}</th>
                        <th class="px-4 py-3">{{ bi('Total Students') }}</th>
                        <th class="px-4 py-3">{{ bi('Total Due') }}</th>
                        <th class="px-4 py-3">{{ bi('Total Collected') }}</th>
                        <th class="px-4 py-3">{{ bi('Total Pending') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $tDue = 0; $tCol = 0; $tPen = 0; $tStu = 0; @endphp
                    @forelse($rows as $i => $row)
                    @php $tDue += $row['due']; $tCol += $row['collected']; $tPen += $row['pending']; $tStu += $row['students']; @endphp
                    <tr wire:key="class-{{ $i }}" class="border-b border-gray-100 dark:border-gray-700">
                        <td class="px-4 py-2 font-medium">{{ $row['class'] }}</td>
                        <td class="px-4 py-2">{{ $row['students'] }}</td>
                        <td class="px-4 py-2">PKR {{ number_format($row['due'], 0) }}</td>
                        <td class="px-4 py-2 text-green-600">PKR {{ number_format($row['collected'], 0) }}</td>
                        <td class="px-4 py-2 text-red-600">PKR {{ number_format($row['pending'], 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ bi('No payments found.') }}</td></tr>
                    @endforelse
                </tbody>
                @if(count($rows))
                <tfoot class="font-semibold border-t-2 border-gray-200 dark:border-gray-600">
                    <tr>
                        <td class="px-4 py-3">{{ bi('Total') }}</td>
                        <td class="px-4 py-3">{{ $tStu }}</td>
                        <td class="px-4 py-3">PKR {{ number_format($tDue, 0) }}</td>
                        <td class="px-4 py-3 text-green-600">PKR {{ number_format($tCol, 0) }}</td>
                        <td class="px-4 py-3 text-red-600">PKR {{ number_format($tPen, 0) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-card>

</x-layouts.backend-layout>
