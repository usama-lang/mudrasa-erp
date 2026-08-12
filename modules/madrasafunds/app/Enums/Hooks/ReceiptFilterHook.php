<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Enums\Hooks;

enum ReceiptFilterHook: string
{
    case QUERY = 'filter.madrasafunds.receipt.query';
    case DATA = 'filter.madrasafunds.receipt.data';
    case REPORT_QUERY = 'filter.madrasafunds.receipt.report_query';
}
