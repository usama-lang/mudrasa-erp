<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 flex flex-col gap-6">

            <x-card>
                <x-slot name="title">{{ bi('Enrollment Details') }}</x-slot>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Student Name') }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $student->student_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Admission No.') }}</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $student->admission_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Campus') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->campus?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Department') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->department?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Class') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->schoolClass?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Section') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->section?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Admission Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->admission_date?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Monthly Fee') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">PKR {{ number_format($student->monthly_fee, 0) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Residential Status') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($student->residential_status === 'resident')
                                <span class="badge badge-primary">{{ bi('Resident') }}</span>
                            @else
                                <span class="badge badge-default">{{ bi('Non-Resident') }}</span>
                            @endif
                        </dd>
                    </div>
                    @if($student->residential_status === 'non_resident')
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Food at Madrasa') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($student->food_at_madrasa)
                                <span class="badge badge-success">{{ bi('Yes – Eats at Madrasa') }}</span>
                            @else
                                <span class="badge badge-default">{{ bi('No – Brings Own Food') }}</span>
                            @endif
                        </dd>
                    </div>
                    @if($student->food_at_madrasa && $student->food_charges)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Food Charges') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">PKR {{ number_format($student->food_charges, 0) }}</dd>
                    </div>
                    @endif
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Enrollment Status') }}</dt>
                        <dd class="mt-1">
                            @php
                                $esBadge = match($student->enrollment_status ?? 'enrolled') {
                                    'enrolled'  => 'badge-success',
                                    'left_self' => 'badge-warning',
                                    'expelled'  => 'badge-danger',
                                    'completed' => 'badge-primary',
                                    default     => 'badge-default',
                                };
                                $esLabel = match($student->enrollment_status ?? 'enrolled') {
                                    'enrolled'  => bi('Enrolled'),
                                    'left_self' => bi('Left (Self)'),
                                    'expelled'  => bi('Expelled'),
                                    'completed' => bi('Completed'),
                                    default     => $student->enrollment_status,
                                };
                            @endphp
                            <span class="badge {{ $esBadge }}">{{ $esLabel }}</span>
                        </dd>
                    </div>
                    @if($student->enrollment_status && $student->enrollment_status !== 'enrolled')
                    @if($student->leaving_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Leaving Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->leaving_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($student->status_remarks)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Status Remarks') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->status_remarks }}</dd>
                    </div>
                    @endif
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Status') }}</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $student->status ? 'badge-success' : 'badge-default' }}">
                                {{ $student->status ? bi('Active') : bi('Inactive') }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </x-card>

            <x-card>
                <x-slot name="title">{{ bi('Personal Details') }}</x-slot>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Father Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->father_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Father CNIC') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->father_cnic ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('B-Form No.') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->b_form_no ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Date of Birth') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->date_of_birth?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Age') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->age ? $student->age . ' ' . bi('years') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Father Profession') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->father_profession ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Phone') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->phone_no ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('WhatsApp') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->whatsapp_no ?? '—' }}</dd>
                    </div>
                    @if($student->current_address)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Current Address') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->current_address }}</dd>
                    </div>
                    @endif
                    @if($student->permanent_address)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ bi('Permanent Address') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->permanent_address }}</dd>
                    </div>
                    @endif
                </dl>
            </x-card>

            @if($student->guardian1_name || $student->guardian2_name)
            <x-card>
                <x-slot name="title">{{ bi('Guardians') }}</x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @if($student->guardian1_name)
                    <div>
                        <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">{{ bi('Guardian 1') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->guardian1_name }}</p>
                        <p class="text-xs text-gray-500">{{ $student->guardian1_relation }}</p>
                        <p class="text-xs text-gray-500">{{ $student->guardian1_phone }}</p>
                    </div>
                    @endif
                    @if($student->guardian2_name)
                    <div>
                        <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">{{ bi('Guardian 2') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->guardian2_name }}</p>
                        <p class="text-xs text-gray-500">{{ $student->guardian2_relation }}</p>
                        <p class="text-xs text-gray-500">{{ $student->guardian2_phone }}</p>
                    </div>
                    @endif
                </div>
            </x-card>
            @endif

        </div>

        <div class="flex flex-col gap-6">

            @if($student->student_image)
            <x-card>
                <x-slot name="title">{{ bi('Photo') }}</x-slot>
                <img src="{{ Storage::url($student->student_image) }}" alt="{{ $student->student_name }}" class="w-full rounded-lg object-cover aspect-square">
            </x-card>
            @endif

            <x-card>
                <x-slot name="title">{{ bi('Account') }}</x-slot>
                @if($student->user)
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $student->user->email }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ bi('Login enabled') }}</p>
                @else
                    <p class="text-sm text-gray-500">{{ bi('No login account') }}</p>
                @endif
            </x-card>

            <x-card>
                <x-slot name="title">{{ bi('Actions') }}</x-slot>
                <div class="flex flex-col gap-2">
                    @can('school_student.edit')
                    <a href="{{ route('school.students.edit', $student->id) }}" wire:navigate class="btn btn-primary w-full text-center">
                        {{ bi('Edit Student') }}
                    </a>
                    @endcan
                    <a href="{{ route('school.students.index') }}" wire:navigate class="btn btn-default w-full text-center">
                        {{ bi('Back to List') }}
                    </a>
                </div>
            </x-card>

        </div>

    </div>

</x-layouts.backend-layout>
