<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <x-card>
        <form method="POST" action="{{ route('school.staff.update', $staff) }}" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
                    {{ bi('Staff Member') }}: {{ $staff->user?->full_name }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">{{ bi('Campus') }}</label>
                        <p class="form-control bg-gray-50 dark:bg-gray-700">{{ $staff->campus?->name }}</p>
                    </div>
                    <div>
                        <label class="form-label">{{ bi('Email') }}</label>
                        <p class="form-control bg-gray-50 dark:bg-gray-700">{{ $staff->user?->email }}</p>
                    </div>
                    <div>
                        <x-inputs.select
                            name="role_type"
                            :label="bi('Role')"
                            :options="$roleTypes"
                            :value="old('role_type', $staff->role_type)"
                            required
                        />
                    </div>
                    <div>
                        <x-inputs.select
                            name="is_active"
                            :label="bi('Status')"
                            :options="[1 => bi('Active'), 0 => bi('Inactive')]"
                            :value="old('is_active', $staff->is_active ? 1 : 0)"
                        />
                    </div>
                </div>
            </div>

            @if(!empty($grantablePermissions))
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
                    {{ bi('Permissions') }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($grantablePermissions as $perm)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                               {{ in_array($perm, old('permissions', $staff->extra_permissions ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-gray-700 dark:text-gray-300">{{ $perm }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex mt-4">
                <x-buttons.submit-buttons cancelUrl="{{ route('school.staff.index') }}" />
            </div>
        </form>
    </x-card>

</x-layouts.backend-layout>
