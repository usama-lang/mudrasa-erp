<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="title">{{ bi('Class Information') }}</x-slot>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolClass->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Level') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolClass->numeric_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Campus') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolClass->campus?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Department') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolClass->department?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Sections') }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $schoolClass->sections->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Students') }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $schoolClass->students->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Status') }}</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $schoolClass->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($schoolClass->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="flex flex-col gap-6">
            <x-card>
                <x-slot name="title">{{ bi('Actions') }}</x-slot>
                <div class="flex flex-col gap-2">
                    @can('school_class.edit')
                    <a href="{{ route('school.classes.edit', $schoolClass->id) }}" wire:navigate class="btn btn-primary w-full text-center">
                        {{ bi('Edit Class') }}
                    </a>
                    @endcan
                    <a href="{{ route('school.classes.index') }}" wire:navigate class="btn btn-default w-full text-center">
                        {{ bi('Back to List') }}
                    </a>
                </div>
            </x-card>
        </div>

    </div>

</x-layouts.backend-layout>
