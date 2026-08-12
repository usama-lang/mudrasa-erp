<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Services;

use App\Services\BaseService;
use Modules\MadrasaFunds\Models\Fund;

class FundService extends BaseService
{
    protected function getModelClass(): string
    {
        return Fund::class;
    }

    /**
     * @return array<int, string>
     */
    public function getFundsDropdown(): array
    {
        return Fund::query()->active()->orderBy('name')->pluck('name', 'id')->toArray();
    }
}
