<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="title">{{ bi('Department Information') }}</x-slot>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $department->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Campus') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $department->campus?->name ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $department->description ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Status') }}</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $department->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($department->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Classes') }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $department->classes->count() }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="flex flex-col gap-6">
            <x-card>
                <x-slot name="title">{{ bi('Actions') }}</x-slot>
                <div class="flex flex-col gap-2">
                    @can('school_department.edit')
                    <a href="{{ route('school.departments.edit', $department->id) }}" wire:navigate class="btn btn-primary w-full text-center">
                        {{ bi('Edit Department') }}
                    </a>
                    @endcan
                    <a href="{{ route('school.departments.index') }}" wire:navigate class="btn btn-default w-full text-center">
                        {{ bi('Back to List') }}
                    </a>
                </div>
            </x-card>
        </div>

    </div>

</x-layouts.backend-layout>
