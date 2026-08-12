<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('schoolmanagement::layouts.admin')]
class Dashboard extends Component
{
    public function render(): View
    {
        return view('schoolmanagement::livewire.admin.dashboard');
    }
}
