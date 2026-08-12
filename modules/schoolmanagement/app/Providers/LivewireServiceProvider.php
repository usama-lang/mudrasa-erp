<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Providers;

use Illuminate\Support\ServiceProvider;

class LivewireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Livewire datatable components removed — replaced with plain JS Ajax datatables
    }
}
