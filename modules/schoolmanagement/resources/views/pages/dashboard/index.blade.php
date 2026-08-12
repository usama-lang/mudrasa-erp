<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @if($isSuperAdmin)
        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-blue-100 dark:bg-blue-900/30 p-3">
                    <iconify-icon icon="lucide:building-2" class="h-6 w-6 text-blue-600 dark:text-blue-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Total Campuses') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['campuses'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.campuses.index') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                {{ bi('Manage Campuses') }} →
            </a>
        </x-card>
        @endif

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-green-100 dark:bg-green-900/30 p-3">
                    <iconify-icon icon="lucide:graduation-cap" class="h-6 w-6 text-green-600 dark:text-green-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Total Students') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['students'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.students.index') }}" class="text-sm text-green-600 hover:underline dark:text-green-400">
                {{ bi('View Students') }} →
            </a>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-purple-100 dark:bg-purple-900/30 p-3">
                    <iconify-icon icon="lucide:user-round-pen" class="h-6 w-6 text-purple-600 dark:text-purple-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Total Teachers') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['teachers'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.teachers.index') }}" class="text-sm text-purple-600 hover:underline dark:text-purple-400">
                {{ bi('View Teachers') }} →
            </a>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-orange-100 dark:bg-orange-900/30 p-3">
                    <iconify-icon icon="lucide:layout-list" class="h-6 w-6 text-orange-600 dark:text-orange-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Departments') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['departments'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.departments.index') }}" class="text-sm text-orange-600 hover:underline dark:text-orange-400">
                {{ bi('View Departments') }} →
            </a>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-teal-100 dark:bg-teal-900/30 p-3">
                    <iconify-icon icon="lucide:book-open" class="h-6 w-6 text-teal-600 dark:text-teal-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Classes') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['classes'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.classes.index') }}" class="text-sm text-teal-600 hover:underline dark:text-teal-400">
                {{ bi('View Classes') }} →
            </a>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-pink-100 dark:bg-pink-900/30 p-3">
                    <iconify-icon icon="lucide:layers" class="h-6 w-6 text-pink-600 dark:text-pink-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Sections') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['sections'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.sections.index') }}" class="text-sm text-pink-600 hover:underline dark:text-pink-400">
                {{ bi('View Sections') }} →
            </a>
        </x-card>

        @can('school_fee.view')
        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-red-100 dark:bg-red-900/30 p-3">
                    <iconify-icon icon="lucide:alert-circle" class="h-6 w-6 text-red-600 dark:text-red-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Pending Monthly Dues') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $feeStats['pending_dues'] ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('school.fees.index') }}" wire:navigate class="text-sm text-red-600 hover:underline dark:text-red-400">
                {{ bi('Collect Fee') }} →
            </a>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-green-100 dark:bg-green-900/30 p-3">
                    <iconify-icon icon="lucide:banknote" class="h-6 w-6 text-green-600 dark:text-green-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('This Month Collection') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">PKR {{ number_format($feeStats['this_month_collection'] ?? 0, 0) }}</p>
                </div>
            </div>
            <a href="{{ route('school.fees.reports.index') }}" wire:navigate class="text-sm text-green-600 hover:underline dark:text-green-400">
                {{ bi('Fee Reports') }} →
            </a>
        </x-card>
        @endcan

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3">
                    <iconify-icon icon="lucide:utensils" class="h-6 w-6 text-amber-600 dark:text-amber-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Non-Resident Eating at Madrasa') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $feeStats['non_resident_eating'] ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 p-3">
                    <iconify-icon icon="lucide:home" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Resident Students') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $feeStats['resident_students'] ?? 0 }}</p>
                </div>
            </div>
        </x-card>

    </div>

</x-layouts.backend-layout>
