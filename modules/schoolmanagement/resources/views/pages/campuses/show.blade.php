<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 flex flex-col gap-6">
            <x-card>
                <x-slot name="title">{{ bi('Campus Information') }}</x-slot>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campus->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Code') }}</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $campus->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Email') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campus->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Phone') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campus->phone ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Address') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campus->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Manager') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campus->manager?->full_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Status') }}</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $campus->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($campus->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="flex flex-col gap-6">
            <x-card>
                <x-slot name="title">{{ bi('Statistics') }}</x-slot>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li class="py-3 flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ bi('Departments') }}</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campus->departments->count() }}</span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ bi('Teachers') }}</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campus->teachers->count() }}</span>
                    </li>
                    <li class="py-3 flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ bi('Students') }}</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campus->students->count() }}</span>
                    </li>
                </ul>
            </x-card>

            <x-card>
                <x-slot name="title">{{ bi('Actions') }}</x-slot>
                <div class="flex flex-col gap-2">
                    @can('school_campus.edit')
                    <a href="{{ route('school.campuses.edit', $campus->id) }}" wire:navigate class="btn btn-primary w-full text-center">
                        {{ bi('Edit Campus') }}
                    </a>
                    @endcan
                    <a href="{{ route('school.campuses.index') }}" wire:navigate class="btn btn-default w-full text-center">
                        {{ bi('Back to List') }}
                    </a>
                </div>
            </x-card>
        </div>

    </div>

</x-layouts.backend-layout>
