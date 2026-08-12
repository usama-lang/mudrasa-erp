<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 flex flex-col gap-6">
            <x-card>
                <x-slot name="title">{{ bi('Teacher Information') }}</x-slot>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Full Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $teacher->user?->full_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Email') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $teacher->user?->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Employee ID') }}</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $teacher->employee_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Campus') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $teacher->campus?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Designation') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $teacher->designation ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Qualification') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $teacher->qualification ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Monthly Salary') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ number_format((float) $teacher->salary, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Joining Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $teacher->joining_date?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Status') }}</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $teacher->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($teacher->status) }}
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
                    @can('school_teacher.edit')
                    <a href="{{ route('school.teachers.edit', $teacher->id) }}" wire:navigate class="btn btn-primary w-full text-center">
                        {{ bi('Edit Teacher') }}
                    </a>
                    @endcan
                    <a href="{{ route('school.teachers.index') }}" wire:navigate class="btn btn-default w-full text-center">
                        {{ bi('Back to List') }}
                    </a>
                </div>
            </x-card>
        </div>

    </div>

</x-layouts.backend-layout>
