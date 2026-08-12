<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @php
        $cards = [
            ['route' => 'admin.madrasafunds.reports.fund',         'label' => mf_bi('Fund-wise Report'),       'icon' => 'lucide:wallet'],
            ['route' => 'admin.madrasafunds.reports.department',   'label' => mf_bi('Department-wise Report'), 'icon' => 'lucide:layout-list'],
            ['route' => 'admin.madrasafunds.reports.student',      'label' => mf_bi('Student Fees Report'),    'icon' => 'lucide:users'],
            ['route' => 'admin.madrasafunds.reports.food',         'label' => mf_bi('Food Fees Report'),       'icon' => 'lucide:utensils'],
            ['route' => 'admin.madrasafunds.reports.consolidated', 'label' => mf_bi('Consolidated Report'),    'icon' => 'lucide:layers'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($cards as $card)
            <a href="{{ route($card['route']) }}" class="block">
                <x-card class="hover:ring-2 hover:ring-primary transition">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary/5 text-primary">
                            <iconify-icon icon="{{ $card['icon'] }}" width="22" aria-hidden="true"></iconify-icon>
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $card['label'] }}</span>
                    </div>
                </x-card>
            </a>
        @endforeach
    </div>

</x-layouts.backend-layout>
