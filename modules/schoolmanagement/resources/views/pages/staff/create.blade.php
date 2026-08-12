<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <x-card>
        <form method="POST" action="{{ route('school.staff.store') }}" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
                    {{ bi('Account Details') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!$campusId)
                    <div class="md:col-span-2">
                        <x-inputs.select
                            name="campus_id"
                            :label="bi('Campus')"
                            :options="['' => bi('-- Select Campus --')] + $campuses"
                            :value="old('campus_id')"
                            required
                        />
                    </div>
                    @else
                    <input type="hidden" name="campus_id" value="{{ $campusId }}">
                    @endif

                    <div>
                        <x-inputs.input
                            name="first_name"
                            :label="bi('First Name')"
                            :value="old('first_name')"
                            required
                        />
                    </div>
                    <div>
                        <x-inputs.input
                            name="last_name"
                            :label="bi('Last Name')"
                            :value="old('last_name')"
                        />
                    </div>
                    <div>
                        <x-inputs.input
                            type="email"
                            name="email"
                            :label="bi('Email')"
                            :value="old('email')"
                            required
                        />
                    </div>
                    <div>
                        <x-inputs.password
                            name="password"
                            :label="bi('Password')"
                            :required="true"
                            :autogenerate="true"
                        />
                    </div>
                    <div>
                        <x-inputs.select
                            name="role_type"
                            :label="bi('Role')"
                            :options="['' => bi('-- Select Role --')] + $roleTypes"
                            :value="old('role_type')"
                            required
                        />
                    </div>
                </div>
            </div>

            @if(!empty($grantablePermissions))
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700 mb-4">
                    {{ bi('Permissions') }}
                    <span class="text-xs font-normal text-gray-500 ml-2">{{ bi('Select permissions to delegate') }}</span>
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($grantablePermissions as $perm)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                               {{ in_array($perm, old('permissions', [])) ? 'checked' : '' }}
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
