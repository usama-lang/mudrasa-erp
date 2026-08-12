<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">{{ bi('Name') }}</th>
                        <th class="px-4 py-3">{{ bi('Email') }}</th>
                        <th class="px-4 py-3">{{ bi('Campus') }}</th>
                        <th class="px-4 py-3">{{ bi('Role') }}</th>
                        <th class="px-4 py-3">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Added By') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium">{{ $member->user?->full_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $member->user?->email ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $member->campus?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="badge badge-primary">{{ str_replace('_', ' ', ucwords($member->role_type, '_')) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($member->is_active)
                                <span class="badge badge-success">{{ bi('Active') }}</span>
                            @else
                                <span class="badge badge-default">{{ bi('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $member->creator?->full_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                @can('school_manager.edit')
                                <a href="{{ route('school.staff.edit', $member) }}" class="btn btn-default btn-xs" title="{{ bi('Edit') }}">
                                    <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
                                </a>
                                @endcan
                                @can('school_manager.delete')
                                <form method="POST" action="{{ route('school.staff.destroy', $member) }}" onsubmit="return confirm('{{ bi('Deactivate this staff member?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="{{ bi('Deactivate') }}">
                                        <iconify-icon icon="lucide:user-minus" width="14"></iconify-icon>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">{{ bi('No staff members found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $staff->links() }}
        </div>
    </x-card>

</x-layouts.backend-layout>
